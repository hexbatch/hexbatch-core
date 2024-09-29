<?php

namespace App\Models;

use App\Enums\Bounds\TypeOfLocation;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;

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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property string bound_name
 * @property TypeOfLocation location_type
 * @property ArrayObject geo_json
 * @property string geom
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property string geom_as_geo_json
 *
 * @property Attribute[] location_attributes
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
        'geo_json' => AsArrayObject::class,
        'location_type' => TypeOfLocation::class,
    ];

    public function location_attributes() : HasMany {
        return $this->hasMany(Attribute::class,'attribute_location_bound_id','id');
    }

    public function getName() {
        return $this->bound_name;
    }

     public static function buildLocationBound(?int $id = null,?int $type_id = null,?int $attribute_id = null) : Builder {

        $build =  LocationBound::select('location_bounds.*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts,ST_AsGeoJSON(geom) as geom_as_geo_json")
            /** @uses LocationBound::location_attributes() */
            ->with('location_attributes');

         if ($attribute_id) {
             $build->join('attributes as bounded_attr',
                 /**
                  * @param JoinClause $join
                  */
                 function (JoinClause $join)  {
                     $join
                         ->on('time_bounds.id','=','bounded_attr.attribute_location_bound_id');
                 }
             );
         }

        if ($type_id) {

            $build->join('attributes as tounded_attr',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('location_bounds.id','=','tounded_attr.attribute_location_bound_id');
                }
            );

            $build->join('element_type_hordes as bounded_horde',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  use($type_id) {
                    $join
                        ->on('bounded_horde.horde_attribute_id','=','tounded_attr.id')
                        ->where('bounded_horde.horde_type_id',$type_id);
                }
            );

        }

        if ($id) {
            $build->where('id',$id);
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

    /**
     * @param string $location_json_to_ping
     * @return bool
     */
    public function ping(string $location_json_to_ping) : bool {
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
        $hit = LocationBound::buildLocationBound(id: $this->id)->whereRaw($where)->first();
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
                }
            }

            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $first_build = LocationBound::buildLocationBound(id: $first_id);
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

}
