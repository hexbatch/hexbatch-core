<?php

namespace App\Models;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Enums\LocationTypes;
use App\Models\Traits\TBoundsCommon;
use App\Rules\GeoJsonPolyReq;
use ArrayObject;
use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPolygon;
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
 * @property LocationTypes location_type
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
    use TBoundsCommon;

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
        'location_type' => LocationTypes::class,
    ];


    public function bound_owner(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'user_id');
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
        return false;
    }

    /**
     * @param string $geo_json
     * @param LocationTypes $shape_type
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setShape(string $geo_json, LocationTypes $shape_type) {
        Validator::make(['location'=>$geo_json], [
            'location'=>['required',new GeoJsonPolyReq],
        ])->validate();

        $this->geo_json = new ArrayObject(json_decode($geo_json,true));
        /** @var Polygon|MultiPolygon $geometry */
        $geometry = GeoJson::jsonUnserialize($this->geo_json);



        $b_is_3d = null;
        //check dimensions
        foreach ($geometry->getCoordinates() as $coord_array) {
            foreach ($coord_array as $coordinates) {
                foreach ($coordinates as $coord) {
                    if (count($coord) > 3 || count($coord) < 2) {
                        throw new HexbatchNotPossibleException(__("msg.location_bound_json_invalid_geo_json"),
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

                }
            }
        }

        if ($shape_type === LocationTypes::SHAPE) {
            //points need to be 3d, but can be any value
            if (!$b_is_3d) {
                throw new HexbatchNotPossibleException(__("msg.location_bounds_shape_is_3d"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::BOUND_TYPE_DEF);
            }
        } elseif ($shape_type === LocationTypes::MAP) {
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
                                throw new HexbatchNotPossibleException(__("msg.location_bound_json_invalid_geo_json"),
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



}
