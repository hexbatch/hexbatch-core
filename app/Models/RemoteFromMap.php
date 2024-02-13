<?php

namespace App\Models;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Enums\Remotes\RemoteFromMapType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int remote_id
 * @property RemoteFromMapType map_type
 * @property string remote_json_path
 * @property string remote_xpath
 * @property string remote_regex_split
 * @property string remote_regex_match
 * @property string holder_json_path
 *
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 *
 */
class RemoteFromMap extends Model
{

    protected $table = 'remote_from_maps';
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
        'map_type' => RemoteFromMapType::class
    ];


    public static function createMap(Collection $c,?Remote $parent = null) : RemoteFromMap {
        $ret = new RemoteFromMap();

        if ($parent) {
            $ret->remote_id = $parent->id;
        }

        if ($c->has('map_type')) {
            $convert = RemoteFromMapType::tryFrom($c->get('map_type'));
            if ($convert) {
                throw new HexbatchNotPossibleException(__("msg.remote_from_map_invalid_type",['ref'=>$c->get('map_type')]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::REMOTE_SCHEMA_ISSUE);
            }
            $ret->map_type = $convert;
        }

        if ($c->has('remote_json_path')) {
            $ret->remote_json_path = $c->get('remote_json_path');
        }

        if ($c->has('remote_xpath')) {
            $ret->remote_json_path = $c->get('remote_xpath');
        }

        if ($c->has('remote_regex_split')) {
            $ret->remote_json_path = $c->get('remote_regex_split');
        }

        if ($c->has('remote_regex_match')) {
            $ret->remote_json_path = $c->get('remote_regex_match');
        }

        if ($c->has('holder_json_path')) {
            $ret->remote_json_path = $c->get('holder_json_path');
        }
        //todo validate types above
        return $ret;
    }

    public function applyRuleToGiven(array $my_data) : array {
        $ret = [];
        //todo add in this code
        return $ret;
    }

}
