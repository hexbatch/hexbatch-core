<?php

namespace App\Models;

use App\Enums\Bounds\TypeOfLocation;
use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;

use App\Rules\BoundNameReq;
use App\Rules\GeoJsonPolyReq;
use App\Rules\GeoJsonReq;
use ArrayObject;
use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPolygon;
use GeoJson\Geometry\Point;
use GeoJson\Geometry\Polygon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int location_bound_namespace_id
 * @property string ref_uuid
 * @property string bound_name
 * @property TypeOfLocation location_type
 * @property ArrayObject display_json
 * @property ArrayObject geo_json
 * @property string geom
 * @property string shape_bounding_box
 * @property string map_bounding_box
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property string geom_as_geo_json
 *
 * @property Attribute[] location_attributes
 * @property UserNamespace location_namespace
 *
 */
class LocationBound extends Model
{


    protected $table = 'location_bounds';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    ];
    protected $casts = [
        'display_json' => AsArrayObject::class,
        'geo_json' => AsArrayObject::class,
        'location_type' => TypeOfLocation::class,
    ];

    public function location_attributes() : HasMany {
        return $this->hasMany(Attribute::class,'attribute_location_bound_id','id');
    }

    public function location_namespace() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'location_bound_namespace_id');
    }

    public function getName() {
        return $this->bound_name;
    }

    public static function getThisLocation(
        ?int             $id = null,
        ?string          $uuid = null
    )
    : LocationBound
    {
        $ret = static::buildLocationBound(me_id:$id,uuid: $uuid)->first();

        if (!$ret) {
            $arg_types = []; $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($uuid) { $arg_types[] = 'uuid'; $arg_vals[] = $uuid;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new HexbatchNotFound(
                __('msg.location_bound_not_found_by',['types'=>$arg_type,'values'=>$arg_val]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::BOUND_NOT_FOUND
            );
        }
        return $ret;
    }

     public static function buildLocationBound(?int $me_id = null, ?int $attribute_id = null, ?string $uuid = null ) : Builder {

        $build =  LocationBound::select('location_bounds.*')
            ->selectRaw(" extract(epoch from  location_bounds.created_at) as created_at_ts,".
                "  extract(epoch from  location_bounds.updated_at) as updated_at_ts,ST_AsGeoJSON(geom) as geom_as_geo_json")
            /** @uses LocationBound::location_attributes(),static::location_namespace() */
            ->with('location_attributes','location_namespace');

        if ($uuid) {
            $build->where('location_bounds.ref_uuid',$uuid);
        }

        if ($attribute_id) {
            $build->join('attributes as bounded_attr',
                 /**
                  * @param JoinClause $join
                  */
                 function (JoinClause $join)  {
                     $join
                         ->on('time_bounds.id','=','bounded_attr.attribute_shape_id');
                 }
            );
        }


        if ($me_id) {
            $build->where('location_bounds.id',$me_id);
        }

        return $build;
    }



    /**
     * @param string $geo_json
     * @param TypeOfLocation $shape_type
     * @return void
     */
    public function setShape(string $geo_json, TypeOfLocation $shape_type) {
        try {
            Validator::make(['location' => $geo_json], [
                'location' => ['required', new GeoJsonPolyReq],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException(__("msg.location_bound_json_invalid_geo_json",['msg'=>__("msg.location_wrong_number_coordinates")])
                . ' : '. $v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_TYPE_DEF);
        }

        $this->geo_json = new ArrayObject(json_decode($geo_json,true));
        /** @var Polygon|MultiPolygon $geometry */
        $geometry = GeoJson::jsonUnserialize($this->geo_json);


        $b_is_3d = null;

        $countCoordinates = function ($coord) use(&$b_is_3d) {
            if (count($coord) > 3 || count($coord) < 2) {
                throw new HexbatchNotPossibleException(__("msg.location_bound_json_invalid_geo_json",['msg'=>__("msg.location_wrong_number_coordinates")]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::BOUND_TYPE_DEF);
            }
            if (count($coord) === 3) {
                if ($b_is_3d === null) {
                    $b_is_3d = true;
                } else {
                    if (!$b_is_3d) {
                        throw new HexbatchNotPossibleException(__("msg.location_bounds_shape_is_3d"),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::BOUND_TYPE_DEF);
                    }
                }

            } else {
                if ($b_is_3d === null) {
                    $b_is_3d = false;
                } else {
                    if ($b_is_3d) {
                        throw new HexbatchNotPossibleException(__("msg.location_bounds_map_is_2d"),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::BOUND_TYPE_DEF);
                    }
                }

            }
        };


        //check dimensions
        foreach ($geometry->getCoordinates() as $coord_array) {
            foreach ($coord_array as $coordinates) {
                if ($geometry->getType() === 'Polygon') {
                    $countCoordinates($coordinates);
                } else {
                    foreach ($coordinates as $coord) {
                        $countCoordinates($coord);
                    }
                }
            }
        }

        if ($shape_type === TypeOfLocation::SHAPE) {
            //points need to be 3d, but can be any value
            if (!$b_is_3d) {
                throw new HexbatchNotPossibleException(__("msg.location_bounds_shape_is_3d"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::BOUND_TYPE_DEF);
            }
        } elseif ($shape_type === TypeOfLocation::MAP) {
            if ($b_is_3d) {
                throw new HexbatchNotPossibleException(__("msg.location_bounds_map_is_2d"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::BOUND_TYPE_DEF);
            }

            $long_sign = null;
            if ($geometry->getType() === 'Polygon') {
                foreach ($geometry->getCoordinates() as $coord_array) {
                    foreach ($coord_array as $coordinates) {
                        foreach ($coordinates as $coord) {
                            $long = $coord[0];
                            $lat = $coord[1];
                            /**
                             * checking for dateline: see if any of the long in each polygon is a different sign
                             * checking poles, make sure all lat is in 1 degree max of each pole, so up to 89 north and -89 south
                             *
                             */
                            if ($long < -180 || $long > 180 || $lat > 89 || $lat < -89) {
                                throw new HexbatchNotPossibleException(__("msg.location_bound_json_invalid_geo_json",['msg'=>__("msg.location_out_of_bounds")]),
                                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                    RefCodes::BOUND_TYPE_DEF);
                            }
                            if ($long_sign === null) {
                                $long_sign = $long < 0;
                            }
                            if ($long_sign !== ($long < 0)) {
                                throw new HexbatchNotPossibleException(__("msg.location_bounds_map_is_2d"),
                                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                    RefCodes::BOUND_TYPE_DEF);
                            }
                        }
                    }
                }
            }
        }

        $this->location_type = $shape_type;
    }


    public function ping(string|array $location_json_to_ping) : bool {
        if (is_array($location_json_to_ping)) {
            $location_json_to_ping = Utilities::maybeDecodeJson($location_json_to_ping);
        }
        try {
            Validator::make(['location' => $location_json_to_ping], [
                'location' => ['required', new GeoJsonReq],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::GEO_JSON_ISSUE);
        }
        $location_geo = json_decode($location_json_to_ping);
        $geometry = GeoJson::jsonUnserialize($location_geo);
        if (!(get_class($geometry) === Polygon::class || get_class($geometry) === MultiPolygon::class || get_class($geometry) === Point::class) ) {
            throw new HexbatchNotPossibleException(__("msg.location_bounds_only_pings_these"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_CANNOT_PING);
        }


        $where = "ST_Contains(geom,ST_AsText(ST_GeomFromGeoJSON('$location_json_to_ping')))";
        $hit = LocationBound::buildLocationBound(me_id: $this->id)->whereRaw($where)->first();
        if ($hit) {
            return true;
        }
        return false;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $build = null;
        $ret = null;
        try {
            if ($field) {
                $ret = $this->where($field, $value)->first();
            } else {
                if (Utilities::is_uuid($value)) {
                    $build = $this->where('ref_uuid', $value);
                } else {
                    if (is_string($value)) {
                        $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);
                        if (count($parts) === 2) {
                            $owner_hint = $parts[0];
                            $maybe_name = $parts[1];
                            /**
                             * @var UserNamespace $owner
                             */
                            $owner = (new UserNamespace())->resolveRouteBinding($owner_hint);
                            $build = $this->where('location_bound_namespace_id', $owner?->id)->where('bound_name', $maybe_name);
                        }

                        if (count($parts) === 3) {
                            $server_hint = $parts[0];
                            $namespace_hint = $parts[1];
                            $maybe_name = $parts[2];
                            /**
                             * @var UserNamespace $owner
                             */
                            $owner = (new UserNamespace())->resolveRouteBinding($server_hint.UserNamespace::NAMESPACE_SEPERATOR.$namespace_hint);
                            $build = $this->where('location_bound_namespace_id', $owner?->id)->where('bound_name', $maybe_name);
                        }
                    }
                }
            }

            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $first_build = LocationBound::buildLocationBound(me_id: $first_id);
                    $ret = $first_build->first();
                }
            }
        }
        catch (\Exception $e) {
            Log::warning('Location Bound resolving: '. $e->getMessage());
        }
        finally {
            if (empty($ret)) {
                throw new HexbatchNotFound(
                    __('msg.bound_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::BOUND_NOT_FOUND
                );
            }
        }
        return $ret;
    }

    /**
     * @throws \Exception
     */
    public static function collectLocationBound(Collection|string $collect, ?UserNamespace $namespace = null,?LocationBound $bound = null)
    : LocationBound
    {
        try {
            DB::beginTransaction();

            if (is_string($collect) && Utilities::is_uuid($collect) && !$bound) {
                /** @var LocationBound $bound */
                $bound = (new LocationBound())->resolveRouteBinding($collect);
            } else {
                if (!$bound) {
                    $bound = new LocationBound();
                }
            }

            if ($namespace) {
                $bound->location_bound_namespace_id = $namespace->id;
            }

            if ($collect->has('bound_name')) {
                $name  = $collect->get('bound_name');
                if (is_string($name) && Str::trim($name)) {
                    try {
                        Validator::make(['location_bound_name' => $name], [
                            'location_bound_name' => ['required', 'string', new BoundNameReq()],
                        ])->validate();
                    } catch (ValidationException $v) {
                        throw new HexbatchNotPossibleException($v->getMessage(),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::BOUND_INVALID_NAME);
                    }
                    $bound->bound_name = Str::trim($name);
                }
            }

            if (!$bound->isInUse()) {
                if ($collect->has('location_type')) {
                    $test_string = $collect->get('location_type');
                    if ($test_string instanceof TypeOfLocation) {
                        $bound->location_type = $test_string;
                    } else {
                        $bound->location_type = TypeOfLocation::tryFromInput($test_string);
                    }

                }


                if ($collect->has('geo_json')) {
                    $what_geo = $collect->get('geo_json');
                    if (Str::isJson($what_geo)) {
                        $what_geo = Utilities::maybeDecodeJson($what_geo,b_associative: true);
                    }
                    if (is_array($what_geo) && !empty($what_geo)) {
                        $bound->geo_json = $what_geo;
                    }
                }
            }

            if ($collect->has('display')) {
                $what_display = $collect->get('display');
                if (Str::isJson($what_display)) {
                    $what_display = Utilities::maybeDecodeJson($what_display,b_associative: true);
                }
                if (is_array($what_display) && !empty($what_display)) {
                    $bound->display_json = $what_display;
                }
            }

            if ((!$bound->bound_name || !$bound->geo_json || !$bound->location_type)) {
                throw new HexbatchCoreException(__("msg.location_bounds_needs_minimum_info"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::BOUND_NEEDS_MIN_INFO);
            }

            if ($bound->location_type && $bound->geo_json) {
                $bound->setShape(json_encode($bound->geo_json), $bound->location_type);
            }


            $bound->save();
            $bound->refresh();
            /** @var LocationBound $bound */
            $bound = LocationBound::buildLocationBound(me_id:$bound->id);

            DB::commit();
            return $bound;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function isInUse() : bool {
        if (!$this->id) {return false;}
        if ($this->location_type === TypeOfLocation::SHAPE) {
            return Attribute::buildAttribute(shape_id: $this->id)->exists();
        }
        return false;
    }

}
