<?php

namespace App\Models;

use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\AttributeUserGroupType;
use App\Models\Enums\AttributeValueType;
use App\Models\Traits\TResourceCommon;
use App\Rules\ResourceNameReq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


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
 *
 * @property Attribute attribute_parent
 * @property User attribute_owner
 * @property TimeBound read_time_bound
 * @property TimeBound write_time_bound
 * @property LocationBound read_map_bound
 * @property LocationBound write_map_bound
 * @property LocationBound read_shape_bound
 * @property LocationBound write_shape_bound
 *
 * @property AttributeMetum[] attribute_meta_default
 * @property AttributeMetum[] attribute_meta_all
 * @property AttributeRule[] da_rules
 * @property AttributeUserGroup[] permission_groups
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

    public function attribute_meta_all() : HasMany {
        return $this->hasMany('App\Models\AttributeMetum','meta_parent_attribute_id','id')
            ->orderBy('meta_type')
            ->orderBy('meta_iso_lang');
    }
    public function attribute_meta_default() : HasMany {
        return $this->hasMany('App\Models\AttributeMetum','meta_parent_attribute_id','id')
            ->where('meta_iso_lang',AttributeMetum::ANY_LANGUAGE)
            ->orderBy('meta_type');
    }

    public function da_rules() : HasMany {
        return $this->hasMany('App\Models\AttributeRule','rule_parent_attribute_id','id')
            ->orderBy('rule_type')
            ->orderBy('target_attribute_id');
    }

    public function permission_groups() : HasMany {
        return $this->hasMany('App\Models\AttributeUserGroup','group_parent_attribute_id','id')
            /** @uses AttributeUserGroup::group_parent() */
            ->with('group_parent')
            ->orderBy('group_type')
            ->orderBy('created_at');
    }

    public function isInUse() : bool {
        if (!$this->id) {return false;}
        return Attribute::where('parent_attribute_id',$this->id)
            ->exists()
            ;
        //todo also check for the element type
    }

    /**
     * @param string $name
     * @param User $owner
     * @return void
     * @throws ValidationException
     */
    public function setName(string $name, User $owner) {
        Validator::make(['attribute_name'=>$name], [
            'attribute_name'=>['required','string','max:128',new ResourceNameReq],
        ])->validate();

        $conflict =  static::where('user_id', $owner->id)->where('attribute_name',$name)->first();
        if ($conflict) {
            throw new HexbatchNameConflictException(__("msg.unique_resource_name_per_user",['resource_name'=>$name]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::RESOURCE_NAME_UNIQUE_PER_USER);
        }

        $this->attribute_name = $name;
    }

    public function setParent(?string $parent_hint) {

        if (empty($parent_hint) ) {
            $this->parent_attribute_id = null;
            return;
        }
        /**
         * @var Attribute $parent
         */
        $parent = (new Attribute())->resolveRouteBinding($parent_hint);
        $user = auth()->user();
        //check if this user can use the parent attribute
        $maybe_group = $this->getPermissionGroup(AttributeUserGroupType::USAGE);
        if ($maybe_group) {
            if (!$maybe_group->target_user_group->isMember($user->id)) {
                throw new HexbatchNameConflictException(__("msg.attribute_cannot_be_used_at_parent",['ref'=>$parent->attribute_name]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_CANNOT_BE_USED_AS_PARENT);
            }
        }
        $this->parent_attribute_id = $parent->id;

    }

    public function getPermissionGroup(AttributeUserGroupType $type_group) : ?AttributeUserGroup{
        foreach ($this->permission_groups as $perm_group) {
            if ($perm_group->group_type === $type_group) {
                return $perm_group;
            }
        }
        return null;
    }

    public static function buildAttribute(
        ?int $id = null,?int $admin_user_id = null)
    : Builder
    {

        $build =  Attribute::select('attributes.*')
            ->selectRaw(" extract(epoch from  attributes.created_at) as created_at_ts,  extract(epoch from  attributes.updated_at) as updated_at_ts")
            /** @uses Attribute::attribute_parent(),Attribute::attribute_owner(),Attribute::read_time_bound(),Attribute::write_time_bound() */
            ->with('attribute_parent', 'attribute_owner', 'read_time_bound', 'write_time_bound')

            /** @uses Attribute::read_map_bound(),Attribute::write_map_bound(),Attribute::read_shape_bound(),Attribute::write_shape_bound() */
            ->with('read_map_bound', 'write_map_bound', 'read_shape_bound', 'write_shape_bound')

            /** @uses Attribute::attribute_meta_default(),Attribute::da_rules(),Attribute::permission_groups() */
            ->with('attribute_meta_default', 'da_rules', 'permission_groups')
       ;

        if ($id) {
            $build->where('attributes.id',$id);
        }


        if ($admin_user_id) {

            $build->join('users',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('users.id','=','attributes.user_id')
                        ->whereNotNull('attributes.user_id');
                }
            );

            $build->join('user_groups',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('user_groups.id','=','users.user_group_id');
                }
            );

            $build->join('user_group_members',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($admin_user_id) {
                    $join
                        ->on('user_group_members.user_group_id','=','user_groups.id')
                        ->where('user_group_members.user_id',$admin_user_id);
                }
            );
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
                            $build = $this->where('user_id', $user?->id)->where('attribute_name', $value);
                        } else {
                            $owner = $parts[0];
                            $maybe_name = $parts[1];
                            $owner = (new User)->resolveRouteBinding($owner);
                            $build = $this->where('user_id', $owner?->id)->where('attribute_name', $maybe_name);
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
