<?php

namespace App\Models;


use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\RemoteUriDataFormatType;
use App\Models\Enums\RemoteUriMethod;
use App\Models\Enums\RemoteUriType;
use App\Models\Traits\TResourceCommon;
use App\Rules\ResourceNameReq;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int user_id
 * @property int usage_group_id
 * @property string ref_uuid
 * @property string remote_name
 * @property boolean is_retired
 * @property boolean is_on
 * @property int timeout_seconds
 * @property RemoteUriType uri_type
 * @property RemoteUriMethod uri_method_type
 * @property RemoteUriDataFormatType uri_data_input_format
 * @property RemoteUriDataFormatType uri_data_output_format
 * @property string uri_string
 * @property int uri_port
 * @property bool is_readable
 * @property bool is_sending_context_to_remote
 * @property bool is_caching
 * @property int cache_ttl_seconds
 * @property ArrayObject cache_keys
 * @property bool is_writable
 * @property int rate_limit_max_per_unit
 * @property int rate_limit_unit_in_seconds
 * @property string rate_limit_starts_at
 * @property int rate_limit_count
 *
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property User remote_owner
 * @property RemoteLog[] logs_of_remote
 * @property RemoteToMap[] rules_to_remote
 * @property RemoteFromMap[] rules_from_remote
 *
 *
 */
class Remote extends Model
{
    use TResourceCommon;
    protected $table = 'remotes';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uri_type' => RemoteUriType::class,
        'uri_method_type' => RemoteUriMethod::class,
        'uri_data_input_format' => RemoteUriDataFormatType::class,
        'uri_data_output_format' => RemoteUriDataFormatType::class,
        'cache_keys' => AsArrayObject::class
    ];


    public function logs_of_remote() : BelongsTo {
        return $this->belongsTo('App\Models\RemoteLog','remote_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts");
    }
    public function rules_to_remote() : BelongsTo {
        return $this->belongsTo('App\Models\RemoteToMap','remote_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts");
    }
    public function rules_from_remote() : BelongsTo {
        return $this->belongsTo('App\Models\RemoteFromMap','remote_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts");
    }

    public function remote_owner() : BelongsTo {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function getName() : string  {
        return $this->remote_owner->username . '.'. $this->attribute_name;
    }

    public function isInUse() : bool {
        if (!$this->id) {return false;}
        $b_exist =  AttributeValuePointer::where('attribute_id',$this->id)->exists();
        if ($b_exist) {return true;}
        return false;
        //todo also check for the  action
    }

    /**
     * @param string $name
     * @param User $owner
     * @return void
     * @throws ValidationException
     */
    public function setName(string $name, User $owner) {
        Validator::make(['remote_name'=>$name], [
            'remote_name'=>['required','string','max:128',new ResourceNameReq],
        ])->validate();

        $conflict =  static::where('user_id', $owner->id)->where('remote_name',$name)->first();
        if ($conflict) {
            throw new HexbatchNameConflictException(__("msg.unique_resource_name_per_user",['resource_name'=>$name]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::RESOURCE_NAME_UNIQUE_PER_USER);
        }

        $this->remote_name = $name;
    }

    public static function buildRemote(
        ?int $id = null,?int $admin_user_id = null)
    : Builder
    {

        $build =  Remote::select('remotes.*')
            ->selectRaw(" extract(epoch from  attributes.created_at) as created_at_ts,  extract(epoch from  attributes.updated_at) as updated_at_ts")
            /** @uses Remote::remote_owner(),Remote::logs_of_remote(),Remote::rules_to_remote(),Remote::rules_from_remote(), */
            ->with('remote_owner','logs_of_remote','rules_to_remote','rules_from_remote')

        ;

        if ($id) {
            $build->where('remotes.id',$id);
        }


        if ($admin_user_id) {

            $build->join('users',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('users.id','=','remotes.user_id');
                }
            );

            $build->join('user_groups',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('user_groups.id','=','users.user_group_id');
                }
            );

            $build->join('user_group_members',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($admin_user_id) {
                    $join
                        ->on('user_group_members.user_group_id','=','user_groups.id')
                        ->where('user_group_members.user_id',$admin_user_id)
                        ->where('user_group_members.is_admin',true);
                }
            );
        }

        return $build;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $build = null;
        $ret = null;
        $first_id = null;
        try {
            if ($field) {
                $build = $this->where($field, $value);
            } else {
                if (Utilities::is_uuid($value)) {
                    //the ref
                    $build = $this->where('ref_uuid', $value);
                } else {
                    if (is_string($value)) {
                        //the name, but scope to the user id of the owner
                        //if this user is not the owner, then the group owner id can be scoped
                        $parts = explode('.', $value);
                        if (count($parts) === 1) {
                            //must be owned by the user
                            $user = auth()->user();
                            $build = $this->where('user_id', $user?->id)->where('remote_name', $value);
                        } else {
                            $owner = $parts[0];
                            $maybe_name = $parts[1];
                            $owner = (new User)->resolveRouteBinding($owner);
                            $build = $this->where('user_id', $owner?->id)->where('remote_name', $maybe_name);
                        }
                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = Remote::buildRemote(id:$first_id)->first();
                }
            }
        } finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.remote_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::REMOTE_NOT_FOUND
                );
            }
        }
        return $ret;

    }

}
