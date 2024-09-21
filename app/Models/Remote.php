<?php

namespace App\Models;


use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\Remotes\RemoteActivityStatusType;
use App\Models\Enums\Remotes\RemoteFromMapType;
use App\Models\Enums\Remotes\RemoteToMapType;
use App\Models\Enums\Remotes\RemoteDataFormatType;
use App\Models\Enums\Remotes\RemoteToSourceType;
use App\Models\Enums\Remotes\RemoteUriMethod;
use App\Models\Enums\Remotes\RemoteUriProtocolType;
use App\Models\Enums\Remotes\RemoteUriType;
use App\Models\Traits\TResourceCommon;
use App\Rules\ResourceNameReq;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use LaLit\Array2XML;
use LaLit\XML2Array;
use Symfony\Component\Yaml\Yaml;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int user_id
 * @property int usage_group_id
 * @property int remote_element_type_id
 * @property int remote_element_id
 * @property string ref_uuid
 * @property string remote_name
 * @property boolean is_retired
 * @property boolean|null is_on
 *
 * @property string created_at
 * @property string updated_at
 *

 *
 * @property int uri_port
 * @property int total_calls_made
 * @property int total_errors
 * @property RemoteUriType uri_type
 * @property RemoteUriMethod uri_method_type
 * @property RemoteDataFormatType to_remote_format
 * @property RemoteDataFormatType from_remote_format
 * @property RemoteUriProtocolType uri_protocol
 * @property string remote_uri_main
 * @property string remote_uri_path
 * @property bool is_caching
 * @property bool is_using_cache_on_failure
 * @property int cache_ttl_seconds
 * @property ArrayObject cache_keys
 * @property ArrayObject xml_doc_type
 * @property string xml_root_name
 * @property int rate_limit_max_per_unit
 * @property int rate_limit_unit_in_seconds
 * @property ?string rate_limit_starts_at
 * @property int rate_limit_count
 * @property int max_concurrent_calls
 *

 * @property User remote_owner
 * @property UserGroup usage_group
 * @property RemoteActivity[] activity_of_remote
 * @property RemoteMetum meta_of_remote
 * @property Remote parent_remote
 * @property RemoteToMap[] rules_to_remote
 * @property RemoteFromMap[] rules_from_remote
 *
 * //in select
 * @property int rate_limit_starts_at_ts
 * @property int created_at_ts
 * @property int updated_at_ts
 */
class Remote extends Model
{
    use TResourceCommon;
    protected $table = 'remotes';
    public $timestamps = false;

    /**
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rate_limit_starts_at',
        'rate_limit_count',
        'remote_call_ended_at' ,
        'remote_activity_status_type',
        'from_remote_processed_data',
        'total_errors',
        'total_calls_made'
    ];

    /**
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uri_type' => RemoteUriType::class,
        'uri_method_type' => RemoteUriMethod::class,
        'to_remote_format' => RemoteDataFormatType::class,
        'from_remote_format' => RemoteDataFormatType::class,
        'uri_protocol' => RemoteUriProtocolType::class,
        'cache_keys' => AsArrayObject::class,
        'xml_doc_type' => AsArrayObject::class
    ];


    public function meta_of_remote() : HasOne {
        return $this->hasOne('App\Models\RemoteMetum','parent_remote_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts")
            /** @uses RemoteMetum::remote_meta_map_bound(),RemoteMetum::remote_meta_time_bound() */
            ->with('remote_meta_time_bound','remote_meta_map_bound')
            ;
    }

    public function usage_group() : HasOne {
        return $this->hasOne('App\Models\UserGroup','id','usage_group_id');
    }



    public function remote_owner() : BelongsTo {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function rules_to_remote() : hasMany {
        return $this->hasMany('App\Models\RemoteToMap','parent_remote_id','id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts");
    }
    public function rules_from_remote() : hasMany {
        return $this->hasMany('App\Models\RemoteFromMap','parent_remote_id','id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts");
    }

    public function getName() : string  {
        return $this->remote_owner->username . '.'. $this->remote_name;
    }

    public function isInUse() : bool {
        if (!$this->id) {return false;}
        $b_exist =  AttributeValuePointer::where('attribute_id',$this->id)->exists();
        if ($b_exist) {return true;}
        $b_exist =  RemoteActivity::where('parent_remote_id',$this->id)->where('remote_activity_status_type',RemoteActivityStatusType::PENDING)->exists();
        if ($b_exist) {return true;}
        return false;
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
            ->selectRaw(" extract(epoch from  remotes.created_at) as created_at_ts,  extract(epoch from  remotes.updated_at) as updated_at_ts,".
                "  extract(epoch from  remotes.rate_limit_starts_at) as rate_limit_starts_at_ts")
            /**
             * @uses Remote::usage_group(),Remote::meta_of_remote(),Remote::rules_to_remote(),Remote::rules_from_remote()
             */
            ->with('usage_group','meta_of_remote','rules_to_remote','rules_from_remote')

        ;

        if ($id) {
            $build->where('remotes.id',$id);
        }


        if ($admin_user_id) {

            $build->join('users owner_user_for_admin_check',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('owner_user_for_admin_check.id','=','remotes.user_id');
                }
            );


            $build->join('user_group_members admin_group_members',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($admin_user_id) {
                    $join
                        ->on('admin_group_members.user_group_id','=','owner_user_for_admin_check.user_group_id')
                        ->where('admin_group_members.user_id',$admin_user_id)
                        ->where('admin_group_members.is_admin',true);
                }
            );
        }


        if ($usage_user_id) {

            $build->join('users as owner_user_for_usage_check',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join->on('owner_user_for_usage_check.id','=','remotes.user_id');
                }
            );


            $build->leftJoin('user_group_members as admin_group_members',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($usage_user_id) {
                    $join
                        ->on('admin_group_members.user_group_id','=','owner_user_for_usage_check.user_group_id')
                        ->where('admin_group_members.user_id',$usage_user_id);
                }
            );

            //remote user group, may not exist

            $build->leftJoin('user_group_members as remote_group_members',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($usage_user_id) {
                    $join
                        ->on('remote_group_members.user_group_id','=','remotes.usage_group_id')
                        ->where('remote_group_members.user_id',$usage_user_id);
                }
            );

            $build->where(function ($q)  {
                $q->whereNotNull('remote_group_members.id')
                    ->orWhereNotNull('admin_group_members.id');
            });


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
                            $user = Utilities::getTypeCastedAuthUser();
                            $build = $this->where('user_id', $user?->id)->where('remote_name', $value);
                        } else {
                            $owner = $parts[0];
                            $maybe_name = $parts[1];
                            /** @var User $owner */
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

    public function powerAdjustRates() {

        if ($this->is_on) {
            $this->resetRateLimit();
        } else {
            $this->update(['rate_limit_starts_at' => null,'rate_limit_count'=>null]);
        }
    }
    public function resetRateLimit() :void {
        $this->update(['rate_limit_starts_at' => DB::raw('NOW()'),'rate_limit_count'=>0]);
    }

    public function addOneToRateLimit() : bool
    {
        if (time() < $this->rate_limit_starts_at_ts + $this->rate_limit_unit_in_seconds) {
            $this->resetRateLimit();
        }
        //see if can add one in the limits, if so do that, otherwise return false
        if ($this->rate_limit_count >= $this->rate_limit_max_per_unit) { return false;} //already at the max

        $this->increment('rate_limit_count');
        return true;
    }



    public function createActivity(Collection $collection,
                                   ?User $user = null,?ElementType $type = null,?Element $element = null,
                                   ?Attribute $attribute = null,
                                   ?array $geo_json = null,
                                   ?array $pass_through = null
    ) :RemoteActivity {
        if ($this->uri_type === RemoteUriType::NONE) {
            throw new \LogicException("Cannot create activity for uri type of none");
        }
        $ret = new RemoteActivity();
        $ret->parent_remote_id = $this->id;
        $ret->caller_user_id = $user?->id;
        $ret->caller_type_id = $type?->id;
        $ret->caller_element_id = $element?->id;
        $ret->caller_attribute_id = $attribute?->id;
        $ret->location_geo_json = $geo_json;
        $ret->to_remote_processed_data = $this->processToSend($collection,RemoteToMapType::DATA,RemoteToSourceType::FROM_DATA);
        $ret->to_headers = $this->processToSend($collection,RemoteToMapType::HEADER,RemoteToSourceType::FROM_DATA);
        $ret->to_remote_files = $this->processToSend($collection,RemoteToMapType::FILE,RemoteToSourceType::FROM_DATA);
        $ret->consumer_passthrough_data = $pass_through;
        $ret->save();



        return $ret;

    }

    const REMOTES_CACHE_TAG = 'remotes';

    const CACHE_KEY_DEFAULT = 'default';
    const CACHE_KEY_NAME_ATTRIBUTE = 'attribute_ref';
    const CACHE_KEY_NAME_ACTION = 'action_ref';
    const CACHE_KEY_NAME_ELEMENT = 'element_ref';
    const CACHE_KEY_NAME_TYPE = 'type_ref';
    const CACHE_KEY_NAME_USER = 'user_ref';
    const CACHE_KEY_NAME_SERVER = 'server_ref';
    const GEO_JSON_DATA_KEY = 'location_geo_json';
    const ALL_SPECIAL_CACHE_KEY_NAMES = [
        self::CACHE_KEY_NAME_ELEMENT,self::CACHE_KEY_NAME_TYPE,self::CACHE_KEY_NAME_ATTRIBUTE,
        self::CACHE_KEY_NAME_ACTION,self::CACHE_KEY_NAME_USER,self::CACHE_KEY_NAME_SERVER
    ];

    public function getRemoteCacheKey() :string {
        return 'r-'.$this->ref_uuid;
    }

    public function getRemoteCache() : array {
        $what = Cache::tags([ static::REMOTES_CACHE_TAG])->get($this->getRemoteCacheKey());
        return Utilities::maybeDecodeJson($what,true,[]);
    }




    public function updateGlobalStats(bool $b_error) :void {
        if ($b_error) {
            $this->update(['total_errors' => $this->total_errors + 1,'total_calls_made' => $this->total_calls_made + 1]);
        } else {
            $this->increment('total_calls_made');
        }
    }



    public function removeSecretsFromArray(ArrayObject|array $my_data,RemoteToMapType $filter) : ?array {
        if (empty($my_data)) { return null;}
        $changes = 0;
        foreach ($this->rules_to_remote as $rule) {
            if ($rule->map_type !== $filter) { continue; }
            if (!$rule->is_secret) { continue; }
            unset($my_data[$rule->remote_data_name]);
            $changes++;
        }
        if (!$changes) {return null;}

        if (is_array($my_data)) {
            return $my_data;
        }
        return $my_data->getArrayCopy();
    }


    public function processToSend(Collection $collection,RemoteToMapType $filter,RemoteToSourceType $source) : array {
        $ret = [];
        $my_data = $collection->toArray();
        foreach ($this->rules_to_remote as $rule) {
            if ($rule->map_type !== $filter) { continue; }
            if ($rule->source_type !== $source) { continue; }
            $pair = $rule->applyRuleToGiven($my_data);
            if (!empty($pair)) {
                $ret = array_merge_recursive($ret,$pair);
            }
        }
        return $ret;
    }



    protected function castResponseToExpected(mixed $what) : array|string|null {
        if (empty($what)) {return null;}

        switch ($this->from_remote_format) {
            case RemoteDataFormatType::TEXT :
            {
                if (is_array($what)) {
                    return implode("\n",$what);
                }
                return (string)$what;
            }
            case RemoteDataFormatType::XML : {
                if (!is_string($what) ) {
                    throw new \LogicException("xml response is not a string");
                }
                try {
                    return XML2Array::createArray($what);
                } catch (\Exception $e) {
                    throw new \RuntimeException("Cannot process xml string: ".$e->getMessage());
                }

            }
            case RemoteDataFormatType::JSON : {
                $maybe_array =  Utilities::maybeDecodeJson($what,true);
                if (!is_array($maybe_array)) {
                    return [$maybe_array];
                }
                return $maybe_array;
            }

            case RemoteDataFormatType::YAML :
            {
                if (!is_string($what) ) {
                    throw new \LogicException("yaml response is not a string");
                }
               try {
                   $value = Yaml::parse($what);
                   if (!is_array($value)) {
                       return [$value];
                   }
                   return $value;
               } catch (\Exception $e) {
                   throw new \RuntimeException("Cannot process yaml string: ".$e->getMessage());
               }
            }
            case RemoteDataFormatType::FORM_URLENCODED:
            case RemoteDataFormatType::MULTIPART_FORM_DATA:
            case RemoteDataFormatType::QUERY:
                throw new \LogicException('From cannot have this value: '. $this->from_remote_format->value);
        }
        throw new \LogicException("Format type mismatch in the code: ". $this->from_remote_format->value);
    }

    public function processDataFromSend(mixed $data,int $code = 0,array $headers = []) : array {
        $ret = [];
        $casted_data = $this->castResponseToExpected($data);
        switch ($this->from_remote_format) {
            case RemoteDataFormatType::TEXT :
            {
                foreach ($this->rules_from_remote as $rule) {
                    if ($rule->map_type !== RemoteFromMapType::DATA) {continue;}
                    $pair = $rule->applyRuleToString($casted_data);
                    if (!empty($pair)) {
                        $ret = array_merge_recursive($ret, $pair);
                    }
                }
                break;
            }
            case RemoteDataFormatType::XML :
            case RemoteDataFormatType::JSON :
            case RemoteDataFormatType::YAML :
            {
                foreach ($this->rules_from_remote as $rule) {
                    if ($rule->map_type !== RemoteFromMapType::DATA) {continue;}
                    $pair = $rule->applyRuleToJson($casted_data);
                    if (!empty($pair)) {
                        $ret = array_merge_recursive($ret, $pair);
                    }
                }
                break;
            }
            case RemoteDataFormatType::FORM_URLENCODED:
            case RemoteDataFormatType::MULTIPART_FORM_DATA:
            case RemoteDataFormatType::QUERY:
                throw new \LogicException('From cannot have this value: '. $this->from_remote_format->value);
        }
        foreach ($this->rules_from_remote as $rule) {
            if ($rule->map_type !== RemoteFromMapType::HEADER) {continue;}
            $pair = $rule->applyRuleToJson($headers);
            if (!empty($pair)) {
                $ret = array_merge_recursive($ret, $pair);
            }
        }

        foreach ($this->rules_from_remote as $rule) {
            if ($rule->map_type !== RemoteFromMapType::RESPONSE_CODE) {continue;}
            $pair = $rule->applyRuleToString(strval($code));
            if (!empty($pair)) {
                $ret = array_merge_recursive($ret, $pair);
            }
        }

        return $ret;
    }

    public function convertToRemoteFormat(array $data) : null|string|array {
        if (empty($data)) {return null;}

        switch ($this->to_remote_format) {
            case RemoteDataFormatType::TEXT: {
                return Utilities::maybeEncodeJson($data);
            }

            case RemoteDataFormatType::JSON:
            case RemoteDataFormatType::QUERY:
            case RemoteDataFormatType::FORM_URLENCODED:
            case RemoteDataFormatType::MULTIPART_FORM_DATA: {
                return $data;
        }


            case RemoteDataFormatType::YAML: {
                return Yaml::dump($data);
            }
            case RemoteDataFormatType::XML: {
                /** @noinspection PhpUnhandledExceptionInspection */
                return Array2XML::createXML($this->xml_root_name??'root',$data,$this->xml_doc_type?->getArrayCopy()??[])->saveXML();
                /*
                 '@docType' => [
                            'name' => 'root',
                            'entities' => null,
                            'notations' => null,
                            'publicId' => '-//W3C//DTD XHTML 1.0 Transitional//EN',
                            'systemId' => 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd',
                            'internalSubset' => null,
                        ],
                 */
            }
        }
        throw new \LogicException("Format type mismatch in the code: ". $this->from_remote_format->value);
    }


}
