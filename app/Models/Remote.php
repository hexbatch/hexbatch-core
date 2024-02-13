<?php

namespace App\Models;


use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchRemoteException;
use App\Exceptions\RefCodes;
use App\Helpers\Remotes\Activity\ActivityEventConsumer;
use App\Helpers\Utilities;
use App\Jobs\RunRemote;
use App\Models\Enums\Remotes\RemoteStatusType;
use App\Models\Enums\Remotes\RemoteToMapType;
use App\Models\Enums\Remotes\RemoteUriDataFormatType;
use App\Models\Enums\Remotes\RemoteUriMethod;
use App\Models\Enums\Remotes\RemoteUriType;
use App\Models\Traits\TResourceCommon;
use App\Rules\ResourceNameReq;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int user_id
 * @property int usage_group_id
 * @property int action_event_id
 * @property int search_path_id
 * @property string ref_uuid
 * @property string remote_name
 * @property boolean is_retired
 * @property boolean is_on
 * @property RemoteUriType uri_type
 * @property RemoteUriMethod uri_method_type
 * @property RemoteUriDataFormatType uri_to_remote_format
 * @property RemoteUriDataFormatType uri_from_remote_format
 * @property string uri_string
 * @property int uri_port
 * @property bool is_sending_context_to_remote
 * @property bool is_caching
 * @property int cache_ttl_seconds
 * @property ArrayObject cache_keys
 * @property int rate_limit_max_per_unit
 * @property int rate_limit_unit_in_seconds
 * @property ?string rate_limit_starts_at
 * @property int rate_limit_count
 * @property int max_concurrent_calls
 * @property int total_calls_made
 * @property int total_errors
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property User remote_owner
 * @property RemoteActivity[] activity_of_remote
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
        'rate_limit_starts_at',
        'rate_limit_count',
        'remote_call_ended_at' ,
        'remote_status_type',
        'from_remote_processed_data',
        'total_errors',
        'total_calls_made'
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
        'uri_to_remote_format' => RemoteUriDataFormatType::class,
        'uri_from_remote_format' => RemoteUriDataFormatType::class,
        'cache_keys' => AsArrayObject::class
    ];

    const REMOTES_CACHE_TAG = 'remotes';

    public function activity_of_remote() : BelongsTo {
        return $this->belongsTo('App\Models\RemoteActivity','remote_id')
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
        ?int $id = null,?int $admin_user_id = null,?int $usage_user_id = null)
    : Builder
    {

        $build =  Remote::select('remotes.*')
            ->selectRaw(" extract(epoch from  attributes.created_at) as created_at_ts,  extract(epoch from  attributes.updated_at) as updated_at_ts")
            /** @uses Remote::remote_owner(),Remote::activity_of_remote(),Remote::rules_to_remote(),Remote::rules_from_remote(), */
            ->with('remote_owner','activity_of_remote','rules_to_remote','rules_from_remote')

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

            $build->join('user_group_members admin_group_members',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($admin_user_id) {
                    $join
                        ->on('admin_group_members.user_group_id','=','user_groups.id')
                        ->where('admin_group_members.user_id',$admin_user_id)
                        ->where('admin_group_members.is_admin',true);
                }
            );
        }


        if ($usage_user_id) {

            $build->join('user_group_members as usage_members',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($usage_user_id) {
                    $join
                        ->on('remotes.usage_group_id','=','usage_members.user_group_id')
                        ->where('usage_members.user_id',$usage_user_id)
                    ;
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

    public function powerRemote(bool $b_off = true) {
        if ($this->is_on === $b_off) {return;}
        $this->is_on = !$b_off;
        $this->save();
        if ($this->is_on) {
            $this->resetRateLimit();
        } else {
            $this->emptyRateLimit();
        }
    }
    public function resetRateLimit() :void {
        $this->update(['rate_limit_starts_at' => DB::raw('NOW()'),'rate_limit_count'=>0]);
    }
    protected function emptyRateLimit() :void {
        $this->update(['rate_limit_starts_at' => null,'rate_limit_count'=>null]);
    }

    protected function addOneToRateLimit() : bool
    {
        if (time() > $this->rate_limit_starts_at + $this->rate_limit_unit_in_seconds) {
            $this->emptyRateLimit();
        }
        //see if can add one in the limits, if so do that, otherwise return false
        if ($this->rate_limit_count >= $this->rate_limit_max_per_unit) { return false;} //already at the max

        $this->increment('rate_limit_count');
        return true;
    }

    public function updateGlobalStats(bool $b_error) :void {
        if ($b_error) {
            $this->update(['total_errors' => $this->total_errors + 1,'total_calls_made' => $this->total_calls_made + 1]);
        } else {
            $this->increment('total_calls_made');
        }


    }


   const CACHE_KEY_DEFAULT = 'default';
   const CACHE_KEY_NAME_ATTRIBUTE = 'attribute_ref';
   const CACHE_KEY_NAME_ACTION = 'action_ref';
   const CACHE_KEY_NAME_ELEMENT = 'element_ref';
   const CACHE_KEY_NAME_TYPE = 'type_ref';
   const CACHE_KEY_NAME_USER = 'user_ref';
   const ALL_SPECIAL_CACHE_KEY_NAMES = [ self::CACHE_KEY_NAME_ELEMENT,self::CACHE_KEY_NAME_TYPE,self::CACHE_KEY_NAME_ATTRIBUTE,self::CACHE_KEY_NAME_ACTION,self::CACHE_KEY_NAME_USER];

    public function getRemoteCacheKey() :string {
        return 'r-'.$this->ref_uuid;
    }

    public function getRemoteCache() : array {
        $what = Cache::tags([ static::REMOTES_CACHE_TAG])->get($this->getRemoteCacheKey());
        return Utilities::maybeDecodeJson($what,true,[]);
    }



    protected function processToSend(Collection $collection,RemoteToMapType $filter) : array {
        $ret = [];
        $my_data = $collection->toArray();
        foreach ($this->rules_to_remote as $rule) {
            if ($rule->map_type !== $filter) { continue; }
            $pair = $rule->applyRuleToGiven($my_data);
            if (!empty($pair)) {
                $ret = array_merge($ret,$pair);
            }
        }
        return $ret;
    }

    public function processFromSend(Collection|array $what) : array {
        $ret = [];
        if (is_object($what) ){
            $my_data = $what->toArray();
        } else {
            $my_data = $what;
        }

        foreach ($this->rules_from_remote as $rule) {
            $pair = $rule->applyRuleToGiven($my_data);
            if (!empty($pair)) {
                $ret = array_merge($ret,$pair);
            }
        }
        return $ret;
    }



    public function createActivity(Collection $collection,
                                   ?User $user = null,?ElementType $type = null,?Element $element = null,
                                   ?Attribute $attribute = null,?Action $action = null,
                                   ?ActivityEventConsumer $consumer = null
    ) :RemoteActivity {

        $ret = new RemoteActivity();
        $ret->remote_id = $this->id;
        $ret->caller_user_id = $user?->id;
        $ret->caller_type_id = $type?->id;
        $ret->caller_element_id = $element?->id;
        $ret->caller_attribute_id = $attribute?->id;
        $ret->caller_action_id = $action?->id;
        $ret->to_remote_processed_data = $this->processToSend($collection,RemoteToMapType::DATA);
        $ret->to_headers = $this->processToSend($collection,RemoteToMapType::HEADER);
        if ($consumer) {
            $ret->consumer_passthrough_data = $consumer->getPassthrough();
        }
        $ret->save();
        $consumer?->setActivity($ret);
        if ($this->addOneToRateLimit()) {
            $ret->remote_status_type = RemoteStatusType::PENDING;
            $ret->save();
            /** @var RemoteActivity $activity_to_process */
            $activity_to_process = RemoteActivity::buildActivity(id:$ret->id)->first();

            if (in_array($this->uri_type,RemoteUriType::DISPATCHABLE_TYPES)) {
                RunRemote::dispatch($activity_to_process);
            }

        } else {
            //try to use cache or just say cannot do this
            $maybe_cache = $ret->getCache();
            if (empty($maybe_cache)) {
                $ret->remote_status_type = RemoteStatusType::FAILED;
                $ret->save();
                throw new HexbatchRemoteException(__("msg.remote_uncallable",['name'=>$this->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::REMOTE_UNCALLABLE);
            }
            $ret->update(['remote_call_ended_at' => DB::raw('NOW()'),'remote_status_type'=>RemoteStatusType::CACHED,'from_remote_processed_data'=>$maybe_cache]);
            $consumer?->markThisDone();
        }

        return $ret;

    }

    public function removeSecretsFromArray(ArrayObject|array $my_data) : ?array {
        if (empty($my_data)) { return null;}
        foreach ($this->rules_to_remote as $rule) {
            if (!$rule->is_secret) { continue; }
            unset($my_data[$rule->remote_data_name]);
        }
        return $my_data;
    }


}
