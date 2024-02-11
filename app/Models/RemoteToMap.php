<?php

namespace App\Models;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\RemoteToMapType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int remote_id
 * @property boolean is_secret
 * @property RemoteToMapType map_type
 * @property string holder_json_path
 * @property string header_var_name
 * @property string header_var_value
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
        'map_type' => RemoteToMapType::class
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
            $ret->holder_json_path = $c->get('holder_json_path');
        }

        if ($c->has('header_var_name')) {
            $ret->header_var_name = $c->get('header_var_name');
        }

        if ($c->has('header_var_value')) {
            $ret->header_var_value = $c->get('header_var_value');
        }

        if ($c->has('is_secret')) {
            $ret->is_secret = Utilities::boolishToBool($c->get('is_secret'));
        }


        //todo validate types above
        return $ret;
    }



}
