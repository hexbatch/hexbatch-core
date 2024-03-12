<?php

namespace App\Models;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\Remotes\RemoteToMapType;
use App\Models\Enums\Remotes\RemoteDataFormatType;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JsonPath\InvalidJsonPathException;
use JsonPath\JsonObject;
use JsonPath\JsonPath;
use LaLit\Array2XML;
use LaLit\XML2Array;
use Symfony\Component\Yaml\Yaml;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_remote_id
 * @property boolean is_secret
 * @property RemoteToMapType map_type
 * @property RemoteDataFormatType cast_data_to_format
 * @property string holder_json_path
 * @property string remote_data_name
 * @property ArrayObject remote_data_constant
 *
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 *
 */
class RemoteToMap extends Model
{

    protected $table = 'remote_to_maps';
    public $timestamps = false;

    const UNJSON_KEY = "@!not_json!@";

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
        'map_type' => RemoteToMapType::class,
        'cast_data_to_format' => RemoteDataFormatType::class,
        'remote_data_constant' => AsArrayObject::class,
    ];

    public static function createMap(Collection $c,?Remote $parent = null) : RemoteToMap {
        $ret = new RemoteToMap();
        if ($parent) {
            $ret->parent_remote_id = $parent->id;
        }
        if ($c->has('map_type')) {
            $convert = RemoteToMapType::tryFrom($c->get('map_type'));
            if (!$convert) {
                throw new HexbatchNotPossibleException(__("msg.remote_from_map_invalid_type",['ref'=>$c->get('map_type')]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::REMOTE_SCHEMA_ISSUE);
            }
            $ret->map_type = $convert;
        }


        if ($c->has('cast_data_to_format')) {
            $convert = RemoteDataFormatType::tryFrom($c->get('cast_data_to_format'));
            if (!$convert) {
                throw new HexbatchNotPossibleException(__("msg.remote_mapped_data_type_wrong",['what'=>$c->get('cast_data_to_format')]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::REMOTE_SCHEMA_ISSUE);
            }
            $ret->cast_data_to_format = $convert ;
        }

        //'' => RemoteDataFormatType::class,
        if ($c->has('is_secret')) {
            $ret->is_secret = Utilities::boolishToBool($c->get('is_secret'));
        }

        if ($c->has('holder_json_path')) {
            $maybe_json_path = $c->get('holder_json_path');
            if (empty($maybe_json_path) || !is_string($maybe_json_path)) {
                $ret->holder_json_path = null;
            } else {
                try {
                    $test = [1,2,3,"apples"=>"two"];//this is just to test, and is ok to keep, this can be any array
                    JsonPath::get($test,$maybe_json_path);
                    $ret->holder_json_path = $maybe_json_path;
                } /** @noinspection PhpRedundantCatchClauseInspection */
                catch (InvalidJsonPathException) {
                    throw new HexbatchNotPossibleException(__("msg.remote_map_invalid_json_path",['ref'=>$maybe_json_path]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::REMOTE_SCHEMA_ISSUE);

                }
            }
        }

        if ($c->has('remote_data_name')) {
            $ret->remote_data_name = $c->get('remote_data_name') ? $c->get('remote_data_name'): null;
        }

        if ($c->has('remote_data_value')) {
            $da_data = $c->get('remote_data_value');
            if (!empty($da_data)) {
                if (!is_array($da_data) && !is_object($da_data)) {
                    if ($ret->is_secret) {
                        $da_data = Utilities::str_encrypt_aes_256_gcm(plaintext: $da_data,password: config('hbc.system_secrets_pw'));
                    }
                    $ret->remote_data_constant = [static::UNJSON_KEY => $da_data];
                } else {
                    if ($ret->is_secret) {
                        throw new HexbatchNotPossibleException(__("msg.remote_map_invalid_secret"),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::REMOTE_SCHEMA_ISSUE);
                    }
                    $ret->remote_data_constant = $da_data;
                }
            }
        }



        return $ret;
    }

    public function getConstantData() : mixed {
        if (empty($this->remote_data_constant)) {return null;}
        if (isset($this->remote_data_constant[static::UNJSON_KEY]) && !empty($this->remote_data_constant[static::UNJSON_KEY])) {
            $val =  $this->remote_data_constant[static::UNJSON_KEY];
            if($this->is_secret) {
                return Utilities::str_decrypt_aes_256_gcm(encrypted_string: $val,password: config('hbc.system_secrets_pw'));
            }
            return $val;
        }
        return $this->remote_data_constant;
    }

    public function applyRuleToGiven(array $my_data) : array {
        $ret = [];
        $data = null;
        $constant_data = $this->getConstantData();
        if ($constant_data)  {
            $data = $constant_data;
        } elseif ($this->holder_json_path) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $jo = new JsonObject($my_data,true);
            $data = $jo->get($this->holder_json_path);
        }
        if (is_null($data) || $data === false ) {return [];}
        //maybe cast this data
        if ($this->cast_data_to_format) {
            switch ($this->cast_data_to_format) {
                case RemoteDataFormatType::TEXT:
                case RemoteDataFormatType::JSON:
                {
                    $data = Utilities::maybeEncodeJson($data);
                    break;
                }

                case RemoteDataFormatType::YAML: {
                    $data = Yaml::dump($data);
                    break;
                }
                case RemoteDataFormatType::XML: {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    $data = Array2XML::createXML($data);
                    break;
                }
            }
        }
        if($this->remote_data_name) {
            $ret[$this->remote_data_name] = $data;
        } else {
            $ret[] = $data;
        }
        return $ret;
    }


}
