<?php

namespace App\Models;


use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

//todo add private and public types and put into the user home set

//todo when the user home set is created from the user type element, its put into the Standard set, all_users

//todo create user_base_attribute

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owner_user_id
 * @property int user_server_id
 * @property int user_type_id
 * @property int public_element_id
 * @property int private_element_id
 * @property int base_user_attribute_id
 * @property int user_home_set_id
 * @property int user_admin_group_id
 * @property string namespace
 *
 * @property string created_at
 * @property string updated_at
 * @property int created_at_ts
 * @property int updated_at_ts
 *
 * @property User owner_user
 * @property ElementType user_base_type
 * @property Server user_home_server
 * @property Element user_public_element
 * @property Element user_private_element
 * @property Attribute user_base_attribute
 * @property ElementSet user_home_set
 * @property UserGroup user_admin_group
 */
class UserType extends Model
{

    protected $table = 'user_types';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

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
    protected $casts = [];

    public function owner_user() : BelongsTo {
        return $this->belongsTo(User::class,'owner_user_id');
    }

    public function user_base_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'user_type_id');
    }

    public function user_home_server() : BelongsTo {
        return $this->belongsTo(Server::class,'user_server_id');
    }

    public function user_public_element() : BelongsTo {
        return $this->belongsTo(Element::class,'public_element_id');
    }

    public function user_private_element() : BelongsTo {
        return $this->belongsTo(Element::class,'private_element_id');
    }

    public function user_base_attribute() : BelongsTo {
        return $this->belongsTo(Attribute::class,'base_user_attribute_id');
    }
    public function user_home_set() : BelongsTo {
        return $this->belongsTo(ElementSet::class,'user_home_set_id');
    }

    public function user_admin_group() : BelongsTo {
        return $this->belongsTo(UserGroup::class,'user_admin_group_id');
    }

    public static function buildUserType(
        ?int $id = null,
        ?int $user_id = null
    )
    : Builder
    {

        $build = UserType::select('user_types.*')
            ->selectRaw(" extract(epoch from  user_types.created_at) as created_at_ts,  extract(epoch from  user_types.updated_at) as updated_at_ts")
            /** @uses UserType::owner_user(),UserType::user_base_type(),UserType::user_home_server(),UserType::user_public_element(),UserType::user_private_element() */
            /** @uses UserType::user_base_attribute(),UserType::user_home_set(),UserType::user_admin_group() */
            ->with('owner_user', 'user_base_type', 'user_home_server', 'user_public_element', 'user_private_element',
                'user_base_attribute', 'user_home_set', 'user_admin_group');

        if ($id) {
            $build->where('user_types.id', $id);
        }

        if ($user_id) {
            $build->where('user_types.owner_user_id', $user_id);
        }
        return $build;
    }

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
                    $build = $this->where('ref_uuid', $value);
                } else {
                    if (is_string($value)) {
                        $build = $this->where('namespace', $value);
                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = UserType::buildUserType(id:$first_id)->first();
                }
            }
        }
        catch (\Exception $e) {
            Log::warning('User Type resolving: '. $e->getMessage());
        }
        finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.user_type_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::USER_TYPE_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    public function getName() : string {
        return $this->namespace;
    }

}
