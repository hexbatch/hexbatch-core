<?php

namespace App\Models;

use App\Enums\Bounds\TypeOfLocation;
use App\Enums\Types\TypeOfLifecycle;
use App\Enums\Types\TypeOfWhitelistPermission;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\ElementTypeNameReq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

//todo type construction (except user tokens) takes place in the owning namespace's home set,
// the rules can react there when creation events to things in the set, the paths in the rules can filter about whose home set we want

//todo I do need to add or remove live types to existing elements
// I cannot change type A, but I can add the rules and element_values of Type B to that specific element of A, and then later remove it too, if I want
// the requirement rules of both A and B still work here, use parent_type live value column on the type_parent table
/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owner_namespace_id
 * @property int type_time_bound_id
 * @property int type_location_map_bound_id
 * @property int type_bound_path_id
 * @property int type_description_element_id
 * @property bool is_system
 * @property bool is_final
 * @property string ref_uuid
 * @property string type_sum_geom_map
 * @property string type_name
 * @property TypeOfLifecycle lifecycle
 *
 * @property UserNamespace owner_namespace
 * @property Attribute[] type_attributes
 * @property ElementType[] type_parents
 * @property ElementTypeHorde[] type_hordes
 * @property ElementTypeWhitelist[] type_whitelists
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property UserNamespace type_owner
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
        'type_name',
        'owner_namespace_id'
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
        'lifecycle'=> TypeOfLifecycle::class
    ];

    public function type_owner() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'owner_namespace_id');
    }



    public function type_attributes() : HasMany {
        return $this->hasMany('App\Models\Attribute','owner_element_type_id','id');
    }

    public function type_whitelists() : HasMany {
        return $this->hasMany(ElementTypeWhitelist::class,'whitelist_owning_type_id','id');
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
        ?int $owner_namespace_id = null
    )
    : Builder
    {

        $build = ElementType::select('element_types.*')
            ->selectRaw(" extract(epoch from  element_types.created_at) as created_at_ts,  extract(epoch from  element_types.updated_at) as updated_at_ts")

            /** @uses ElementType::type_owner(), ElementType::type_attributes(), ElementType::type_whitelists() */
            /** @uses ElementType::type_children(),ElementType::type_parents() */
            ->with('type_owner', 'type_attributes', 'type_children', 'type_parents','type_whitelists')
            ;

        if ($id) {
            $build->where('element_types.id', $id);
        }
        if ($owner_namespace_id) {
            $build->where('element_types.owner_namespace_id', $owner_namespace_id);
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
                        $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);
                        if (count($parts) >= 2) {
                            $owner_hint = $parts[0];
                            $maybe_name = $parts[1];
                            /**
                             * @var UserNamespace $owner
                             */
                            $owner = (new UserNamespace())->resolveRouteBinding($owner_hint);
                            $build = $this->where('owner_namespace_id', $owner?->id)->where('type_name', $maybe_name);
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
                    __('msg.type_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::TYPE_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    public function getName() :string {
        return $this->type_owner->getName().UserNamespace::NAMESPACE_SEPERATOR.$this->type_name;
    }

    public function isInUse() : bool {
        if (Element::where('element_parent_type_id',$this->id)->count() ) {return true;}
        if (ElementTypeParent::where('parent_type_id',$this->id)->count() ) {return true;}
        if (ElementTypeHorde::where('horde_type_id',$this->id)->count() ) {return true;}

        return false;
    }

    public function canNamespaceEdit(UserNamespace $namespace) : bool {
        return (bool)$this->type_owner?->isNamespaceAdmin($namespace) ;
    }

    public function canNamespaceViewDetails(UserNamespace $namespace) : bool {
        return (bool)$this->type_owner?->isNamespaceMember($namespace) ;
    }

    public function canNamespaceInherit(UserNamespace $namespace) : bool {
        if (empty($this->type_whitelists)) {return true;}
        if ($this->type_owner->isNamespaceAdmin($namespace)) {return true;}
        return ElementTypeWhitelist::where('whitelist_owning_type_id',$this->id)
            ->where('whitelist_namespace_id',$namespace->id)
            ->where('whitelist_permission',TypeOfWhitelistPermission::INHERITING)->exists();
    }


    public function sumMapFromAttributes() {
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

    }

    /**
     * @throws ValidationException
     */
    public function editType(Collection $collect) : void {
        try
        {
            DB::beginTransaction();

            if ($this->isInUse()) {
                //todo update this
                $this->is_final = Utilities::boolishToBool($collect->get('is_final',false));
                $this->save();
                return;
            }


            if ($collect->has('parents')) {

                collect($collect->get('parents'))->each(function ($some_parent_hint, int $key) {
                    Utilities::ignoreVar($key);
                    if (!is_string($some_parent_hint)) {
                        throw new HexbatchNotPossibleException(__('msg.child_types_must_be_string_names'),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::TYPE_BAD_SCHEMA);
                    }
                    /**
                     * @var ElementType|null $some_parent
                     */
                    $some_parent = (new ElementType())->resolveRouteBinding($some_parent_hint);
                    $user_namespace = Utilities::getCurrentNamespace();
                    //todo update this
                    if ( $some_parent->is_final || !$some_parent->canNamespaceInherit($user_namespace)) {
                        throw new HexbatchNotPossibleException(__('msg.child_type_is_not_inheritable'),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::TYPE_CANNOT_INHERIT);
                    }
                    ElementTypeParent::addParent($some_parent, $this);
                });
            }

            if ($collect->has('type_name')) {
                try {
                    if ($this->type_name = $collect->get('type_name')) {
                        Validator::make(['type_name' => $this->type_name], [
                            'type_name' => ['required', 'string', new ElementTypeNameReq($this->current_type)],
                        ])->validate();
                    }
                } catch (ValidationException $v) {
                    throw new HexbatchNotPossibleException($v->getMessage(),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::TYPE_INVALID_NAME);
                }
            }

            if (!$this->type_name && !$this->id) {
                throw new HexbatchNotPossibleException(__('msg.type_must_have_name'),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::TYPE_INVALID_NAME);
            }


            if ($collect->has('time_bound')) {
                $hint_time_bound = $collect->get('time_bound');
                if (is_string($hint_time_bound) || $hint_time_bound instanceof Collection) {
                    $time_bound = TimeBound::collectTimeBound($hint_time_bound);
                    $this->type_time_bound_id = $time_bound->id;
                }
            }

            if ($collect->has('map_bound')) {
                $hint_location_bound = $collect->get('map_bound');
                if (is_string($hint_location_bound) || $hint_location_bound instanceof Collection) {
                    $bound = LocationBound::collectLocationBound($hint_location_bound);
                    if ($bound->location_type === TypeOfLocation::SHAPE) {
                        throw new HexbatchNotPossibleException(__('msg.type_must_have_map_bound'),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::TYPE_BAD_SCHEMA);
                    }
                    $this->type_location_map_bound_id = $bound->id;
                }
            }


            if ($collect->has('path_bound')) {
                $hint_path_bound = $collect->get('path_bound');
                if (is_string($hint_path_bound) || $hint_path_bound instanceof Collection) {
                    $path = Path::collectPath($hint_path_bound);
                    $this->type_bound_path_id = $path->id;
                }
            }
            $this->save();

            collect($collect->get('attributes'))->each(function ($some_attribute_hint, int $key) {
                Utilities::ignoreVar($key);
                Attribute::collectAttribute($some_attribute_hint,$this);
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
