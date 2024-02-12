<?php

namespace App\Models;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\Remotes\RemoteToMapType;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JsonPath\InvalidJsonPathException;
use JsonPath\JsonObject;
use JsonPath\JsonPath;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int remote_id
 * @property boolean is_secret
 * @property RemoteToMapType map_type
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
        'remote_data_constant' => AsArrayObject::class
    ];

    public static function createMap(Collection $c,?Remote $parent = null) : RemoteToMap {
        $ret = new RemoteToMap();
        if ($parent) {
            $ret->remote_id = $parent->id;
        }
        if ($c->has('map_type')) {
            $convert = RemoteToMapType::tryFrom($c->get('map_type'));
            if ($convert) {
                throw new HexbatchNotPossibleException(__("msg.remote_from_map_invalid_type",['ref'=>$c->get('map_type')]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::REMOTE_SCHEMA_ISSUE);
            }
            $ret->map_type = $convert;
        }

        if ($c->has('holder_json_path')) {
            $maybe_json_path = $c->get('holder_json_path');
            if (empty($maybe_json_path) || !is_string($maybe_json_path)) {
                $ret->holder_json_path = null;
            } else {
                try {
                    $ret = [1,2,3,"apples"=>"two"];
                    JsonPath::get($ret,$maybe_json_path);
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
                    $ret->remote_data_value = [static::UNJSON_KEY => $da_data];
                } else {
                    $ret->remote_data_value = $da_data;
                }
            }
        }

        if ($c->has('is_secret')) {
            $ret->is_secret = Utilities::boolishToBool($c->get('is_secret'));
        }

        return $ret;
    }

    public function getConstantData() : mixed {
        if (empty($this->remote_data_constant)) {return null;}
        if (isset($this->remote_data_constant[static::UNJSON_KEY]) && !empty($this->remote_data_constant[static::UNJSON_KEY])) {
            return $this->remote_data_constant[static::UNJSON_KEY];
        }
        return $this->remote_data_constant;
    }

    public function applyRuleToGiven(array $my_data) : array {
        $ret = [];
        $path = $this->holder_json_path;
        $constant_data = $this->getConstantData();
        if ($constant_data)  {
            $ret[$this->remote_data_name] = $constant_data;
        } elseif ($path) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $jo = new JsonObject($my_data,true);
            $found_data = $jo->get($path);
            if ($found_data !== false) {
                $ret[$this->remote_data_name] = $found_data;
            }
        }
        return $ret;
    }


}
