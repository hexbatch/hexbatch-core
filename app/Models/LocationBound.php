<?php

namespace App\Models;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Enums\Bounds\LocationType;
use App\Models\Traits\TResourceCommon;
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
use Illuminate\Support\Facades\Validator;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int user_id
 * @property boolean is_retired
 * @property string bound_name
 * @property LocationType location_type
 * @property ArrayObject geo_json
 * @property string geom
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property string geom_as_geo_json
 * @property User bound_owner
 *
 */
class LocationBound extends Model
{
    use TResourceCommon;

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
        'location_type' => LocationType::class,
    ];


    public function bound_owner(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function getName() {
        return $this->bound_owner->username . '.' .$this->bound_name;
    }

     public static function buildLocationBound(?int $user_id = null,?int $id = null) : Builder {
        /** @var Builder $build */
        $build =  LocationBound::select('location_bounds.*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts,ST_AsGeoJSON(geom) as geom_as_geo_json");

        if ($user_id) {
            $build->where('user_id',$user_id);
        }

        if ($id) {
            $build->where('id',$id);
        }

        return $build;
    }

    public function isInUse() : bool {
        if (!$this->id) {return false;}
        return Attribute::where('read_map_location_bounds_id',$this->id)
            ->orWhere('write_map_location_bounds_id',$this->id)
            ->orWhere('read_shape_location_bounds_id',$this->id)
            ->orWhere('write_shape_location_bounds_id',$this->id)
            ->exists()
            ;
    }

    /**
     * @param string $geo_json
     * @param LocationType $shape_type
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setShape(string $geo_json, LocationType $shape_type) {
        Validator::make(['location'=>$geo_json], [
            'location'=>['required',new GeoJsonPolyReq],
        ])->validate();

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

        if ($shape_type === LocationType::SHAPE) {
            //points need to be 3d, but can be any value
            if (!$b_is_3d) {
                throw new HexbatchNotPossibleException(__("msg.location_bounds_shape_is_3d"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::BOUND_TYPE_DEF);
            }
        } elseif ($shape_type === LocationType::MAP) {
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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ping(string $location_json_to_ping) : bool {
        Validator::make(['location'=>$location_json_to_ping], [
            'location'=>['required',new GeoJsonReq],
        ])->validate();
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


}
