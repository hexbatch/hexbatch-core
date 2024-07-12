<?php

namespace App\Models;

use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Attributes\Apply\StandardAttributes;
use App\Helpers\Utilities;
use App\Http\Resources\AttributeMetaResource;
use App\Http\Resources\AttributeRuleResource;
use App\Http\Resources\UserGroupResource;
use App\Models\Enums\Attributes\AttributeRuleType;
use App\Models\Enums\Attributes\AttributeUserGroupType;
use App\Models\Enums\Attributes\AttributeValueType;
use App\Models\Traits\TResourceCommon;
use App\Rules\ResourceNameReq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
//todo attributes can have ancestors, which will provide missing  data in the definition if that attribute is missing it
// each meta, rule, group, and the lookup are inherited unless the attribute defines its own section for those
// the section can add or remove rules, meta, group without changing the rest of the inherited, or can simply replace, or make empty

/*
 * todo When an attribute is live on an element or used in an event, then its parent is the attribute type
 * todo make a new table for live attributes that has the attribute, and its json value (put pointer value in there or other primitive if not json type)
 *  only one live attribute is used for each element or event, unique possession
 *
 * todo a fired event is a live attribute whose json values is fed into one or more stacks (defined by the action handlers)
 *  an event can only be fired if there is an action for it
 *  remove events table, and make waiting_actions table which has the attribute created for the event
 *
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int read_time_bounds_id
 * @property int write_time_bounds_id
 * @property int read_map_location_bounds_id
 * @property int write_map_location_bounds_id
 * @property int read_shape_location_bounds_id
 * @property int write_shape_location_bounds_id
 * @property boolean is_read_policy_all
 * @property boolean is_write_policy_all
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 *
 * @property TimeBound read_time_bound
 * @property TimeBound write_time_bound
 * @property LocationBound read_map_bound
 * @property LocationBound write_map_bound
 * @property LocationBound read_shape_bound
 * @property LocationBound write_shape_bound
 * @property AttributeValuePointer value_pointer
 *
 */
class AttributeBound extends Model
{
    use TResourceCommon;

    protected $table = 'attribute_bounds';
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
    protected $casts = [];


    public function read_time_bound() : BelongsTo {
        return $this->belongsTo('App\Models\TimeBound','read_time_bounds_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  bound_start) as bound_start_ts,  extract(epoch from  bound_stop) as bound_stop_ts");
    }

    public function write_time_bound() : BelongsTo {
        return $this->belongsTo('App\Models\TimeBound','write_time_bounds_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  bound_start) as bound_start_ts,  extract(epoch from  bound_stop) as bound_stop_ts");
    }

    public function read_map_bound() : BelongsTo {
        return $this->belongsTo('App\Models\LocationBound','read_map_location_bounds_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts,ST_AsGeoJSON(geom) as geom_as_geo_json");
    }

    public function write_map_bound() : BelongsTo {
        return $this->belongsTo('App\Models\LocationBound','write_map_location_bounds_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts,ST_AsGeoJSON(geom) as geom_as_geo_json");
    }

    public function read_shape_bound() : BelongsTo {
        return $this->belongsTo('App\Models\LocationBound','read_shape_location_bounds_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts,ST_AsGeoJSON(geom) as geom_as_geo_json");
    }

    public function write_shape_bound() : BelongsTo {
        return $this->belongsTo('App\Models\LocationBound','write_shape_location_bounds_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts,ST_AsGeoJSON(geom) as geom_as_geo_json");

    }








    public static function buildAttributeBound(
        ?int $id = null)
    : Builder
    {

        $build =  AttributeBound::select('attribute_bounds.*')
            ->selectRaw(" extract(epoch from  attribute_bounds.created_at) as created_at_ts,  extract(epoch from  attribute_bounds.updated_at) as updated_at_ts")
            /** @uses AttributeBound::read_time_bound(),AttributeBound::write_time_bound() */
            ->with('read_time_bound', 'write_time_bound')

            /** @uses AttributeBound::read_map_bound(),AttributeBound::write_map_bound(),AttributeBound::read_shape_bound(),AttributeBound::write_shape_bound() */
            ->with('read_map_bound', 'write_map_bound', 'read_shape_bound', 'write_shape_bound')
       ;

        if ($id) {
            $build->where('attribute_bounds.id',$id);
        }

        return $build;
    }

}
