<?php

namespace App\Models;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\AttributeValueType;
use App\Models\Traits\TResourceCommon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int user_id
 * @property int parent_attribute_id
 * @property int read_time_bounds_id
 * @property int write_time_bounds_id
 * @property int read_map_location_bounds_id
 * @property int write_map_location_bounds_id
 * @property int read_shape_location_bounds_id
 * @property int write_shape_location_bounds_id
 * @property boolean is_retired
 * @property boolean is_constant
 * @property boolean is_static
 * @property boolean is_final
 * @property boolean is_human
 * @property boolean is_read_policy_all
 * @property boolean is_write_policy_all
 * @property boolean is_nullable
 * @property AttributeValueType value_type
 * @property int value_numeric_min
 * @property int value_numeric_max
 * @property string value_regex
 * @property string value_default
 * @property string attribute_name
 * @property string created_at
 * @property string updated_at
 *
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property Attribute attribute_parent
 * @property User attribute_owner
 * @property TimeBound read_time_bound
 * @property TimeBound write_time_bound
 * @property LocationBound read_map_bound
 * @property LocationBound write_map_bound
 * @property LocationBound read_shape_bound
 * @property LocationBound write_shape_bound
 */
class Attribute extends Model
{
    use TResourceCommon;

    protected $table = 'attributes';
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
        'value_type' => AttributeValueType::class,
    ];

    public function attribute_parent() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','parent_attribute_id');
    }

    public function attribute_owner() : BelongsTo {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function read_time_bound() : BelongsTo {
        return $this->belongsTo('App\Models\TimeBound','read_time_bounds_id');
    }

    public function write_time_bound() : BelongsTo {
        return $this->belongsTo('App\Models\TimeBound','write_time_bounds_id');
    }

    public function read_map_bound() : BelongsTo {
        return $this->belongsTo('App\Models\LocationBound','read_map_location_bounds_id');
    }

    public function write_map_bound() : BelongsTo {
        return $this->belongsTo('App\Models\LocationBound','write_map_location_bounds_id');
    }

    public function read_shape_bound() : BelongsTo {
        return $this->belongsTo('App\Models\LocationBound','read_shape_location_bounds_id');
    }

    public function write_shape_bound() : BelongsTo {
        return $this->belongsTo('App\Models\LocationBound','write_shape_location_bounds_id');
    }

    public function isInUse() : bool {
        if (!$this->id) {return false;}
        return Attribute::where('parent_attribute_id',$this->id)
            ->exists()
            ;
        //todo also check for the element type
    }

    public static function buildAttribute(?int $id = null,?int $readable_user = null) : Builder {

        $build =  Attribute::select('attributes.*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts")
            /** @uses Attribute::attribute_parent(),Attribute::attribute_owner(),Attribute::read_time_bound(),Attribute::write_time_bound() */
            ->with('attribute_parent attribute_owner read_time_bound write_time_bound')

            /** @uses Attribute::read_map_bound(),Attribute::write_map_bound(),Attribute::read_shape_bound(),Attribute::write_shape_bound() */
            ->with('read_map_bound write_map_bound read_shape_bound write_shape_bound')
       ;

        if ($id) {
            $build->where('id',$id);
        }

        if ($readable_user) {
            //condition on the user group join read
            //todo implement more
        }

        return $build;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $build = null;
        $ret = null;
        $first_id = null;
        try {
            if ($field) {
                $build = $this->where($field, $value);
            } else {
                if (Utilities::is_uuid($value)) {
                    //the ref
                    $build = $this->where('ref_uuid', $value);
                } else {
                    if (is_string($value)) {
                        //the name, but scope to the user id of the owner
                        //if this user is not the owner, then the group owner id can be scoped
                        $parts = explode('.', $value);
                        if (count($parts) === 1) {
                            //must be owned by the user
                            $user = auth()->user();
                            $build = $this->where('user_id', $user?->id)->where('bound_name', $value);
                        } else {
                            $owner = $parts[0];
                            $maybe_name = $parts[1];
                            $owner = (new User)->resolveRouteBinding($owner);
                            $build = $this->where('user_id', $owner?->id)->where('bound_name', $maybe_name);
                        }
                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = Attribute::buildAttribute(id:$first_id)->first();
                }
            }
        } finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.attribute_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::ATTRIBUTE_NOT_FOUND
                );
            }
        }
        return $ret;

    }

}
