<?php

namespace Models;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchRemoteException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Jobs\RunRemote;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementType;
use App\Models\Server;
use App\Models\User;
use ArrayObject;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Remotes\RemoteActivityStatusType;
use Remotes\RemoteCachePolicyType;
use Remotes\RemoteCacheStatusType;
use Remotes\RemoteDataFormatType;
use Remotes\RemoteToMapType;
use Remotes\RemoteUriMethod;
use Remotes\RemoteUriProtocolType;
use Remotes\RemoteUriType;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_remote_id
 * @property int remote_stack_id
 * @property int caller_action_id
 * @property int caller_attribute_id
 * @property int caller_user_id
 * @property int caller_element_id
 * @property int caller_type_id
 * @property int caller_server_id
 * @property string ref_uuid
 * @property RemoteActivityStatusType remote_activity_status_type
 * @property RemoteCacheStatusType cache_status_type
 * @property RemoteCachePolicyType cache_policy_type
 * @property int data_priority_level_in_stack
 * @property int response_code
 * @property ArrayObject to_headers
 * @property ArrayObject from_headers
 * @property ArrayObject from_remote_processed_data
 * @property ArrayObject to_remote_processed_data
 * @property ArrayObject to_remote_files
 * @property ArrayObject errors
 * @property ArrayObject location_geo_json
 * @property ArrayObject consumer_passthrough_data
 * @property string from_remote_raw_text
 *
 *
 *
 * @property string remote_call_ended_at
 * @property string created_at
 * @property string updated_at
 *
 * @property int remote_call_ended_at_ts
 * @property int created_at_ts
 * @property int updated_at_ts
 *
 * @property Remote remote_parent
 * @property RemoteStack home_stack

 * @property Attribute caller_attribute
 * @property User caller_user
 * @property Server caller_server
 * @property Element caller_element
 * @property ElementType caller_type
 *
 */
class RemoteActivity extends Model
{

    protected $table = 'remote_activities';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'remote_call_ended_at'
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
        'to_headers' => AsArrayObject::class,
        'from_headers' => AsArrayObject::class,
        'from_remote_processed_data' => AsArrayObject::class,
        'to_remote_processed_data' => AsArrayObject::class,
        'to_remote_files' => AsArrayObject::class,
        'errors' => AsArrayObject::class,
        'consumer_passthrough_data' => AsArrayObject::class,
        'location_geo_json' => AsArrayObject::class,
        'remote_activity_status_type' => RemoteActivityStatusType::class,
        'cache_status_type' => RemoteCacheStatusType::class,
        'cache_policy_type' => RemoteCachePolicyType::class

    ];

    public function remote_parent() : BelongsTo {
        return $this->belongsTo('Models\Remote','parent_remote_id')->select('remotes.*')
            ->selectRaw(" extract(epoch from  remotes.created_at) as created_at_ts,  extract(epoch from  remotes.updated_at) as updated_at_ts");
    }

    public function home_stack() : BelongsTo {
        $what =  $this->belongsTo('Models\RemoteStack','remote_stack_id');
        RemoteStack::decorateBuilder($what);
        return $what;
    }


    public function caller_attribute() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','caller_attribute_id')
            ->select('attributes.*')
            ->selectRaw(" extract(epoch from  attributes.created_at) as created_at_ts,  extract(epoch from  attributes.updated_at) as updated_at_ts");
    }

    public function caller_user() : BelongsTo {
        return $this->belongsTo('App\Models\User','caller_user_id')
            ->select('users.*')
            ->selectRaw(" extract(epoch from  users.created_at) as created_at_ts,  extract(epoch from  users.updated_at) as updated_at_ts");
    }

    public function caller_server() : BelongsTo {
        return $this->belongsTo('App\Models\Server','caller_server_id');
    }

    public function caller_element() : BelongsTo {
        return $this->belongsTo('App\Models\Element','caller_element_id')
            /** @uses Element::element_owner() */
            ->with('element_owner');
    }

    public function caller_type() : BelongsTo {
        return $this->belongsTo('App\Models\ElementType','caller_type_id');
    }


    public static function buildActivity(
        ?int $id = null, ?RemoteActivityStatusType $remote_activity_status_type = null,
        ?RemoteCacheStatusType $cache_status_type = null, ?RemoteUriType $uri_type = null,
        array $remote_id_array = []
    )
    : Builder
    {


        $build = RemoteActivity::select('remote_activities.*')
            ->selectRaw(" extract(epoch from  remote_activities.created_at) as created_at_ts,  extract(epoch from  remote_activities.updated_at) as updated_at_ts".
                ",  extract(epoch from  remote_activities.remote_call_ended_at) as remote_call_ended_at_ts")

            /** @uses RemoteActivity::remote_parent(),RemoteActivity::home_stack(),Remote::rules_to_remote(),Remote::rules_from_remote() */
            ->with('remote_parent','remote_parent.rules_to_remote','remote_parent.rules_from_remote','home_stack')

            /**
             * @uses RemoteActivity::caller_attribute(),RemoteActivity::caller_user(),RemoteActivity::caller_server()
             * @uses RemoteActivity::caller_element(),RemoteActivity::caller_type(),
             */
            ->with('caller_attribute','caller_user','caller_element','caller_type','caller_server')
        ;

        if ($id) {
            $build->where('remote_activities.id', $id);
        }
        //do join to parent for common causes
        if ($uri_type) {

            $build->join('remotes as par_remote',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join->on('par_remote.id','=','remote_activities.parent_remote_id');
                }
            );
        }



        if (count($remote_id_array) ) {
            $build->whereIn('remote_activities.parent_remote_id',$remote_id_array);
        }

        if ($remote_activity_status_type) {
            $build->where('remote_activities.remote_activity_status_type', $remote_activity_status_type->value);
        }
        if ($cache_status_type) {
            $build->where('remote_activities.cache_status_type', $cache_status_type->value);
        }

        if ($uri_type) {
            $build->where('par_remote.uri_type',$uri_type->value);
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
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = RemoteActivity::buildActivity(id:$first_id)->first();
                }
            }
        } finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.remote_activity_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::REMOTE_ACTIVITY_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    public function getName() :string {
        return $this->ref_uuid;
    }

    public function getCacheSubKey() : string {
        $subkeys = [];
        foreach ($this->remote_parent->cache_keys as $some_key) {
            if (in_array($some_key,Remote::ALL_SPECIAL_CACHE_KEY_NAMES)) {
                $maybe = match($some_key) {
                    Remote::CACHE_KEY_NAME_ATTRIBUTE => $this->caller_attribute?->ref_uuid,
                    Remote::CACHE_KEY_NAME_ELEMENT => $this->caller_element?->ref_uuid,
                    Remote::CACHE_KEY_NAME_TYPE => $this->caller_type?->ref_uuid,
                    Remote::CACHE_KEY_NAME_USER => $this->caller_user?->ref_uuid,
                    Remote::CACHE_KEY_NAME_SERVER => $this->caller_server?->ref_uuid,
                };
                if ($maybe) {$subkeys[] = $maybe;}
            } else {
                $subkeys[] = $some_key;
            }
        }
        if (empty($subkeys)) {
            $subkey = Remote::CACHE_KEY_DEFAULT;
        } else {
            $subkey = implode('_',$subkeys);
        }
        return $subkey;
    }

    public function getCache() : array {
        $cache = $this->remote_parent->getRemoteCache();
        $subkey = $this->getCacheSubKey();
        return $cache[$subkey]??[] ;
    }

    /**
     * @throws \Exception
     */
    public function addCache() : void {
        try {
            if (!$this->remote_parent->is_caching) {
                $this->cache_status_type = RemoteCacheStatusType::NOT_MADE;
                return;
            }
            $older_cache = $this->remote_parent->getRemoteCache();
            $subkey = $this->getCacheSubKey();
            $older_cache[$subkey] = $this->from_remote_processed_data ?? [];

            $final = Utilities::wrapJsonEncode($older_cache);
            Cache::tags([Remote::REMOTES_CACHE_TAG])
                ->put($this->remote_parent->getRemoteCacheKey(), $final, $this->remote_parent->cache_ttl_seconds);
            $this->cache_status_type = RemoteCacheStatusType::CREATED;
        } catch (\Exception $e) {
            $this->cache_status_type = RemoteCacheStatusType::ERROR;
            throw $e;
        }
    }


    protected function addError(\Exception $e,mixed $data = []) {
        $node = ['message'=>$e->getMessage(),'class'=>get_class($e),'data'=>$data];
        Log::error("Remote activity error",$node);
        if (is_array($this->errors) && count($this->errors)) {
            $this->errors[] = $node;
        } else {
            $this->errors = $node;
        }
    }

    public function announceDaFinishing() :void  {
        $this->home_stack?->stack_finalization();
    }

    protected function getGuzzleToFormat() :string  {
        switch ($this->remote_parent->to_remote_format) {
            case RemoteDataFormatType::XML:
            case RemoteDataFormatType::TEXT:
            case RemoteDataFormatType::YAML: return 'body';
            case RemoteDataFormatType::JSON: return 'json';
            case RemoteDataFormatType::QUERY: return 'query';
            case RemoteDataFormatType::MULTIPART_FORM_DATA: return 'multipart';
            case RemoteDataFormatType::FORM_URLENCODED: return 'form_params';
        }
        throw new \LogicException("Missing cases");
    }

    public function runActivity() {
        if ($this->remote_activity_status_type !== RemoteActivityStatusType::PENDING) {
            throw new \RuntimeException("Activity #$this->id is not pending its ". $this->remote_activity_status_type->value);
        }
        if ($this->remote_parent->addOneToRateLimit()) {
            $this->remote_activity_status_type = RemoteActivityStatusType::PENDING;
            $this->save();
            /** @var RemoteActivity $activity_to_process */
            $activity_to_process = RemoteActivity::buildActivity(id:$this->id)->first();

            if (in_array($this->remote_parent->uri_type,RemoteUriType::DISPATCHABLE_TYPES)) {
                RunRemote::dispatch($activity_to_process->id);
            }

        } else {
            //try to use cache or just say cannot do this
            $maybe_cache = $this->getCache();
            if (empty($maybe_cache)) {
                $this->remote_activity_status_type = RemoteActivityStatusType::FAILED;
                $this->save();
                throw new HexbatchRemoteException(__("msg.remote_uncallable",['name'=>$this->remote_parent->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::REMOTE_UNCALLABLE);
            }
            $this->update(['remote_call_ended_at' => DB::raw('NOW()'),'remote_activity_status_type'=>RemoteActivityStatusType::CACHED,'from_remote_processed_data'=>$maybe_cache]);
        }
    }

    public function doCallRemote(mixed $manual_data = null) {
        $this->remote_activity_status_type = RemoteActivityStatusType::STARTED;
        $this->save();
        $b_error = false;
        try {
            $from_data = [];
            $code = 0;
            $headers = [];
            $temp_files = [];
            try {

                switch ($this->remote_parent->uri_type) {
                    case RemoteUriType::NONE: {
                        throw new \LogicException("Cannot process uri type of none");
                    }
                    case RemoteUriType::URL: {
                        if ($this->remote_parent->uri_protocol === RemoteUriProtocolType::NONE) {
                            throw new \LogicException("cannot have a protocol of none");
                        }
                        $protocal = $this->remote_parent->uri_protocol->value;
                        $url_base = $protocal. '://'.$this->remote_parent->remote_uri_main;
                        if ($this->remote_parent->uri_port) {
                            $url_base .= ":".$this->remote_parent->uri_port;
                        }
                        if ($this->remote_parent->remote_uri_path) {
                            $url = $url_base . '/'.ltrim($this->remote_parent->remote_uri_path,"/");
                        } else {
                            $url = $url_base;
                        }

                        $data = $this->to_remote_processed_data;

                        $options = [];
                        $guzzle_to_format = $this->getGuzzleToFormat();

                        switch ($this->remote_parent->uri_method_type) {

                            case RemoteUriMethod::GET:
                            case RemoteUriMethod::DELETE:
                            case RemoteUriMethod::PATCH:
                            case RemoteUriMethod::POST:
                            case RemoteUriMethod::PUT: {
                                $data_out = [];
                                if ($this->remote_parent->to_remote_format !== RemoteDataFormatType::QUERY) {
                                    $data[static::IDENTIFYING_DATA_KEY] = $this->getIdentifyingData();
                                }
                                if ($this->remote_parent->to_remote_format === RemoteDataFormatType::MULTIPART_FORM_DATA) {
                                    foreach ($data as $data_key => $data_val) {
                                        $data_out[] = [
                                            'name'     => $data_key,
                                            'contents' => (is_array($data_val)? Utilities::maybeEncodeJson($data_val):$data_val),
                                        ];
                                    }
                                    foreach (($this->to_remote_files??[]) as $k => $file_contents) {
                                        $tmpfname = tempnam(sys_get_temp_dir(), 'remote-' . $this->remote_parent . '-' . $k . '-');
                                        $temp_files[] = $tmpfname;
                                        $handle = fopen($tmpfname, "w");
                                        fwrite($handle, $file_contents);
                                        fclose($handle);
                                        $data_out[] = [
                                            'name'     => $k,
                                            'contents' => \GuzzleHttp\Psr7\Utils::tryFopen($tmpfname, 'r'),
                                            'filename' => $k
                                        ];
                                    }
                                } else {
                                    $data_out = $this->remote_parent->convertToRemoteFormat($data->getArrayCopy());
                                }

                                $options[$guzzle_to_format] = $data_out;
                                break;
                            }
                            case RemoteUriMethod::NONE:
                                throw new \LogicException("Uri method is set to none");
                        }


                        $options['headers'] = $this->to_headers->getArrayCopy();

                        $client = new Client();

                        $response =$client->request($this->remote_parent->uri_method_type->value,$url,$options);

                        $code= $response->getStatusCode();
                        $from_data = (string)$response->getBody();
                        $headers = $response->getHeaders();
                        break;
                    }
                    case RemoteUriType::CONSOLE: {


                        $command_args_array = [];
                        foreach ($this->to_remote_processed_data as $k => $v) {
                            if (!is_array($v)) {
                                $args = [$v];
                            } else {
                                $args = $v;
                            }
                            if (is_int($k)) {
                                foreach ($args as $what_arg) {
                                    $command_args_array[] = $what_arg;
                                }

                            } else {
                                foreach ($args as $what_key => $what_arg) {
                                    if (is_int($what_key)) {
                                        $command_args_array[] = $what_arg;
                                    } else {
                                        $command_args_array[] = "$what_key$what_arg";
                                    }

                                }

                            }
                        } //end foreach args

                        foreach ($this->to_remote_files as $k => $file_contents) {
                            $tmpfname = tempnam(sys_get_temp_dir(),'remote-'.$this->remote_parent.'-'.$k .'-');
                            $temp_files[] = $tmpfname;
                            $handle = fopen($tmpfname, "w");
                            fwrite($handle, $file_contents);
                            fclose($handle);
                            $command_args_array[$k] = $tmpfname;
                        }
                        $command_args = $this->remote_parent->remote_uri_path . implode(' ',$command_args_array);
                        $command = $this->remote_parent->remote_uri_main . ' '. $command_args . " 2>&1" ;
                        $this->runCommand($command,$from_data);
                        break;
                    }
                    case RemoteUriType::CODE: {
                        $class = $this->remote_parent->remote_uri_main;
                        $method = $this->remote_parent->remote_uri_path;

                        try {
                            if (empty($this->to_remote_processed_data)) {
                                $from_data = $class::$method();
                            } else {
                                $data = $this->to_remote_processed_data;
                                $data[static::IDENTIFYING_DATA_KEY] = $this->getIdentifyingData();
                                $data_array = $data->getArrayCopy();
                                $from_data = $class::$method(...$data_array);
                            }
                        } catch (\Error|\Exception $e) {
                            throw new \RuntimeException("Could not execute code for remote: ". $e->getMessage(),$e->getCode(),$e);
                        }

                        break;
                    }

                    case RemoteUriType::MANUAL_OWNER:
                    case RemoteUriType::MANUAL_ELEMENT: {
                        $from_data = $manual_data;
                        break;
                    }
                }

                $this->from_headers = $headers;
                $this->from_remote_raw_text = Utilities::maybeEncodeJson($from_data);
                $this->from_remote_processed_data = $this->remote_parent->processDataFromSend($from_data,$code,$headers);
                $this->response_code = $code;
                $this->remote_activity_status_type = RemoteActivityStatusType::SUCCESS;
            } catch (GuzzleException|\Exception $e) {
                $b_error = true;
                $this->addError($e,$from_data);
                $this->remote_activity_status_type = RemoteActivityStatusType::FAILED;
            }

            if (!$b_error) {
                try {
                    $this->addCache();
                } catch (\Exception $e) {
                    $this->addError($e);
                }
            }
        } finally {
            $changes = $this->remote_parent->removeSecretsFromArray($this->to_remote_processed_data,RemoteToMapType::DATA);
            if ($changes) {$this->to_remote_processed_data = $changes;}

            $changes = $this->remote_parent->removeSecretsFromArray($this->to_remote_processed_data,RemoteToMapType::FILE);
            if ($changes) { $this->to_remote_files = $changes; }

            $changes = $this->remote_parent->removeSecretsFromArray($this->to_remote_processed_data,RemoteToMapType::HEADER);
            if ($changes) { $this->to_headers = $changes; }

            $this->save();
            $this->update(['remote_call_ended_at' => DB::raw('NOW()')]);
            $this->remote_parent->updateGlobalStats($b_error);
            foreach ($temp_files as $da_file) {
                unlink($da_file);
            }
        }
        $this->announceDaFinishing();
    }

    public function runCommand($command,array &$output) : void  {

        $raw_output = [];
        $b_exec_ok = exec($command,$raw_output,$result_code);
        if ($b_exec_ok === false ) { throw new \RuntimeException("[runCommand] Could not run: $command ");}

        $output = [];
        if (!empty($raw_output)) {
            foreach ($raw_output as $out) {
                //it is in ansi color and formatting, impossible to read as text until those are removed first
                $poc_line = trim(Utilities::cleanAnsiFromString($out));
                if ($poc_line) {
                    $output[] = $poc_line;
                }
            }
        }

        if ($result_code) {
            throw new \RuntimeException("$command has error code $result_code: ",$result_code);
        }
    }

    const IDENTIFYING_DATA_KEY = 'call_identity';
    public function getIdentifyingData() : array {
        $ret = [];
        if ($this->caller_user) { $ret[Remote::CACHE_KEY_NAME_USER] = $this->caller_user->ref_uuid;}
        if ($this->caller_element) { $ret[Remote::CACHE_KEY_NAME_ELEMENT] = $this->caller_element->ref_uuid;}

        if ($this->caller_type) { $ret[Remote::CACHE_KEY_NAME_TYPE] = $this->caller_type->ref_uuid;}
        if ($this->caller_attribute) { $ret[Remote::CACHE_KEY_NAME_ATTRIBUTE] = $this->caller_attribute->ref_uuid;}
        if ($this->caller_server) { $ret[Remote::CACHE_KEY_NAME_SERVER] = $this->caller_server->ref_uuid;}
        if (!empty($this->location_geo_json )) { $ret[Remote::GEO_JSON_DATA_KEY] = $this->location_geo_json;}

        return $ret;
    }

}
