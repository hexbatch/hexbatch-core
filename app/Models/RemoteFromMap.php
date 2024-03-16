<?php

namespace App\Models;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\Remotes\RemoteFromMapType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JsonPath\InvalidJsonPathException;
use JsonPath\JsonObject;
use JsonPath\JsonPath;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_remote_id
 * @property RemoteFromMapType map_type
 * @property string remote_json_path
 * @property string remote_regex_match
 * @property string remote_data_name
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
        'map_type' => RemoteFromMapType::class,
    ];


    public static function createMap(Collection $c,?Remote $parent = null) : RemoteFromMap {
        $ret = new RemoteFromMap();

        if ($parent) {
            $ret->parent_remote_id = $parent->id;
        }

        if ($c->has('map_type')) {
            $convert = RemoteFromMapType::tryFrom($c->get('map_type'));
            if (!$convert) {
                throw new HexbatchNotPossibleException(__("msg.remote_from_map_invalid_type",['ref'=>$c->get('map_type')]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::REMOTE_SCHEMA_ISSUE);
            }
            $ret->map_type = $convert;
        }


        if ($c->has('remote_json_path')) {
            $maybe_json_path = $c->get('remote_json_path');
            if (empty($maybe_json_path) || !is_string($maybe_json_path)) {
                $ret->remote_json_path = null;
            } else {
                try {
                    $test = [1,2,3,"apples"=>"two"];//this is just to test, and is ok to keep, this can be any array
                    JsonPath::get($test,$maybe_json_path);
                    $ret->remote_json_path = $maybe_json_path;
                } /** @noinspection PhpRedundantCatchClauseInspection */
                catch (InvalidJsonPathException) {
                    throw new HexbatchNotPossibleException(__("msg.remote_map_invalid_json_path",['ref'=>$maybe_json_path]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::REMOTE_SCHEMA_ISSUE);

                }
            }
        }



        if ($c->has('remote_regex_match')) {

            $maybe_regex_match= $c->get('remote_regex_match');
            if (empty($maybe_regex_match) || !is_string($maybe_regex_match)) {
                $ret->remote_regex_match = null;
            } else {
                $errors_of_regex = Utilities::regexHasErrors($maybe_regex_match);
                if ($errors_of_regex) {
                    throw new HexbatchNotPossibleException(__("msg.remote_map_invalid_regex",['ref'=>$maybe_regex_match]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::REMOTE_SCHEMA_ISSUE);
                }
                $ret->remote_regex_match = $maybe_regex_match;
            }
        }

        if ($c->has('remote_data_name')) {
            $ret->remote_data_name = $c->get('remote_data_name') ? $c->get('remote_data_name'): null;
        }

        return $ret;
    }


    public function applyRuleToJson(array $my_data) : array {
        if (empty($this->remote_json_path) ) return [];
        $ret = [];
        $path = $this->remote_json_path;
        /** @noinspection PhpUnhandledExceptionInspection */
        $jo = new JsonObject($my_data,true);
        $found_data = $jo->get($path);
        if ($found_data !== false) {
            if($this->remote_data_name) {
                $ret[$this->remote_data_name] = $found_data;
            } else {
                $ret[] = $found_data;
            }
        }
        return $ret;
    }

    public function applyRuleToString(string $my_data) : array {
        if (empty($this->remote_regex_match) ) return [];
        $ret = [];
        $matches = [];
        $preg_ret = preg_match_all($this->remote_regex_match,$my_data,$matches,PREG_SET_ORDER);
        if (empty($preg_ret)) {return [];}
        if (empty($matches)) {return [];}
        //look at matches, if any element has a two-dimensional array greater than 1, then we return all the non-zero index
        //else we return the concatenated 0 index

        $vals = [];
        //first pass look at return size
        $b_submatches = false;
        foreach ($matches as $m) {
            if (count($m) > 1) {$b_submatches = true; break;}
        }

        if ($b_submatches) {
            foreach ($matches as $m) {
                if (!is_array($m)) {continue;}

                for($i = 1; $i < count($m); $i++) {
                    if (!isset($m[$i])) { break;}
                    $vals[] = $m[$i];
                }
            }
        } else {
            foreach ($matches as $m) {
                if (is_array($m)) {
                    $vals[] = $m[0];
                } else {
                    $vals[] = $m;
                }

            }
        }

        if($this->remote_data_name) {
            $ret[$this->remote_data_name] = $vals;
        } else {
            $ret[] = $vals;
        }


        return $ret;
    }

}
