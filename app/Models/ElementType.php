<?php

namespace App\Models;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

//todo type construction (except user tokens) takes place in the user's home set, the rules can react there when creation events to things in the set
/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int editing_user_group_id
 * @property int inheriting_user_group_id
 * @property int new_elements_user_group_id
 * @property int type_read_user_group_id
 * @property int type_write_user_group_id
 *
 *
 * @property int type_start_ts
 * @property int type_end_ts
 * @property int type_next_period_starts_ts
 *
 * @property string type_sum_geom_map
 * @property string type_sum_geom_shape
 *
 *
 * @property string ref_uuid
 * @property int user_id
 * @property boolean is_retired
 * @property boolean is_system
 * @property boolean is_final
 * @property string type_name
 * @property string created_at
 * @property string updated_at
 *
 * @property User type_owner
 * @property Attribute[] type_attributes
 * @property ElementType[] type_parents
 * @property ElementTypeHorde[] type_hordes
 * @property UserGroup editing_group
 * @property UserGroup inheriting_group
 * @property UserGroup new_elements_group
 * @property UserGroup read_whitelist_group
 * @property UserGroup write_whitelist_group
 *
 */
class ElementType extends Model
{

    protected $table = 'element_types';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_retired',
        'type_name',
        'user_id'
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

    public function type_owner() : BelongsTo {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function editing_group() : BelongsTo {
        return $this->belongsTo(UserGroup::class,'editing_user_group_id');
    }

    public function inheriting_group() : BelongsTo {
        return $this->belongsTo(UserGroup::class,'inheriting_user_group_id');
    }

    public function new_elements_group() : BelongsTo {
        return $this->belongsTo(UserGroup::class,'new_elements_user_group_id');
    }

    public function read_whitelist_group() : BelongsTo {
        return $this->belongsTo(UserGroup::class,'type_read_user_group_id');
    }

    public function write_whitelist_group() : BelongsTo {
        return $this->belongsTo(UserGroup::class,'type_write_user_group_id');
    }

    public function type_attributes() : HasMany {
        return $this->hasMany('App\Models\Attribute','owner_element_type_id','id');
    }

    public function type_hordes() : HasMany {
        return $this->hasMany(ElementTypeHorde::class,'horde_type_id','id')
            /**
             * @uses ElementTypeHorde::horde_attribute()
             */
            ->with('horde_attribute');
    }
    public function type_children() : HasMany {
        return $this->hasMany(ElementType::class,'parent_type_id','id');
    }

    public function type_parents() : HasMany {
        return $this->hasMany(ElementTypeParent::class,'child_type_id','id');
    }

    public static function buildElementType(
        ?int $id = null,
        ?int $user_id = null
    )
    : Builder
    {

        $build = ElementType::select('element_types.*')
            ->selectRaw(" extract(epoch from  element_types.created_at) as created_at_ts,  extract(epoch from  element_types.updated_at) as updated_at_ts")

            /** @uses ElementType::type_owner(),ElementType::editing_group(),ElementType::inheriting_group(),ElementType::new_elements_group(),ElementType::type_attributes() */
            /** @uses ElementType::type_children(),ElementType::type_parents(),ElementType::read_whitelist_group(),ElementType::write_whitelist_group() */
            ->with('type_owner', 'editing_group', 'inheriting_group', 'new_elements_group','type_attributes',
                'type_parents','type_children','read_whitelist_group','write_whitelist_group')
            ;

        if ($id) {
            $build->where('element_types.id', $id);
        }
        if ($user_id) {
            $build->where('element_types.user_id', $user_id);
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
                        $parts = explode('.', $value);
                        if (count($parts) >= 2) {
                            $owner_hint = $parts[0];
                            $maybe_name = $parts[1];
                            /**
                             * @var User $owner
                             */
                            $owner = (new User)->resolveRouteBinding($owner_hint);
                            $build = $this->where('user_id', $owner?->id)->where('type_name', $maybe_name);
                        }
                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = ElementType::buildElementType(id:$first_id)->first();
                }
            }
        }
        catch (\Exception $e) {
            Log::warning('Element Type resolving: '. $e->getMessage());
        }
        finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.element_type_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::ELEMENT_TYPE_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    public function getName() :string {
        return $this->type_owner->username.'.'.$this->type_name;
    }

    public function isInUse() : bool {
        if (Element::where('element_parent_type_id',$this->id)->count() ) {return true;}
        if (ElementTypeParent::where('parent_type_id',$this->id)->count() ) {return true;}
        if (ElementTypeHorde::where('horde_type_id',$this->id)->count() ) {return true;}
        return false;
    }

    public function canUserEdit(User $user) : bool {
        if ($this->type_owner?->inAdminGroup($user->id) ) { return true; }
        if ($this->editing_group?->isMember($user->id) ) { return true; }
        return false;
    }

    public function canUserViewDetails(User $user) : bool {
        if ($this->type_owner?->inAdminGroup($user->id) ) { return true; }
        if ($this->editing_group?->isMember($user->id) ) { return true; }
        if ($this->inheriting_group?->isMember($user->id) ) { return true; }
        return false;
    }

    public function canUserInherit(User $user) : bool {
        return $this->canUserViewDetails($user);
    }

    public static function updateAggregatedStats() {
        //update for each type that has a bound:
         // type_start_ts,type_end_ts,type_next_period_starts_ts,type_sum_geom_map,type_sum_geom_shape

        //todo the time spans may better better off as ranges:
            //https://www.postgresql.org/docs/current/rangetypes.html
            //
        //todo implement the current or next time range of span

        //todo update the element_values when doing the start and stop

        //todo parent types have option to restrict their children to keep the same bounds for time, and same or lesser bounds for map
           //over
        //do spans
        // get time spans for the bounded types , the other cron jobs will have created and trimmed them

        // get the current one, or the next one if not one current now and put into type_start_ts and type_end_ts
        // put the start of the next one in the type_next_period_starts_ts
        Attribute::whereNotNull('attribute_time_bound_id')
            ->join('time_bound_spans',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join)  {
                    $join
                        ->on('attributes.attribute_time_bound_id','=','time_bound_spans.time_bound_id')
                        ->orWhere(function ($q)  {
                            $q->whereRaw('time_bound_spans.span_start > extract(epoch from  NOW())')
                            ->WhereRaw('time_bound_spans.span_stop < extract(epoch from  NOW())');
                        })
                        ->orWhere(function ($q)  {
                            $q->whereRaw('time_bound_spans.span_start > extract(epoch from  NOW())');
                        });
                }
            );



    }

    public function sumGeoFromAttributes() {
        //then for the attributes that have a map, do a union of their geometries and store in type_sum_geom_map
        $id = $this->id;
        DB::statement("
            UPDATE element_types
            SET type_sum_geom_map=subquery.sum_geo

            FROM (
                    SELECT t.id as element_type_id , ST_Union(b.geom) as sum_geo
                    FROM  element_types t
                    INNER JOIN attributes a  ON a.owner_element_type_id = t.id
                    INNER JOIN location_bounds b  ON a.attribute_location_bound_id = b.id AND b.location_type = 'map'
                    WHERE t.id = $id
                    GROUP BY t.id
                    ) AS subquery
            WHERE element_types.id=subquery.element_type_id;
        ");


        //then for the attributes that have a shape, do a union of their geometries and store in type_sum_geom_shape

        DB::statement("
            UPDATE element_types
            SET type_sum_geom_shape=subquery.sum_geo

            FROM (
                    SELECT t.id as element_type_id , ST_Union(b.geom) as sum_geo
                    FROM  element_types t
                    INNER JOIN attributes a  ON a.owner_element_type_id = t.id
                    INNER JOIN location_bounds b  ON a.attribute_location_bound_id = b.id AND b.location_type = 'shape'
                    WHERE t.id = $id
                    GROUP BY t.id
                    ) AS subquery
            WHERE element_types.id=subquery.element_type_id;
        ");
    }
}
