<?php

namespace App\Models;

use App\Models\Enums\ELocationType;
use App\Models\Traits\TBoundsCommon;
use App\Rules\GeoJsonPolyReq;
use ArrayObject;
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
 * @property ELocationType location_type
 * @property ArrayObject original
 * @property string geom
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
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
        'options' => AsArrayObject::class,
        'location_type' => ELocationType::class,
    ];


    public function bound_owner(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
     public static function buildLocationBound(?int $user_id = null,?int $id = null) : Builder {
        /** @var Builder $build */
        $build =  LocationBound::select('location_bounds.*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts");

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
     * @param ELocationType $shape_type
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setShape(string $geo_json,ELocationType $shape_type) {
        Validator::make(['location'=>$geo_json], [
            'location'=>['required',new GeoJsonPolyReq],
        ])->validate();
        $this->original = json_decode($geo_json,true);
        $this->location_type = $shape_type;
    }



}
