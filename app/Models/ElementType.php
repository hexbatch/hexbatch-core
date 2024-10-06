<?php

namespace App\Models;

use App\Enums\Bounds\TypeOfLocation;
use App\Enums\Things\TypeOfThingStatus;
use App\Enums\Types\TypeOfLifecycle;
use App\Enums\Types\TypeOfWhitelistPermission;
use App\Exceptions\HexbatchCoreException;
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




/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owner_namespace_id
 * @property int imported_from_server_id
 * @property int type_time_bound_id
 * @property int type_location_map_bound_id
 * @property int type_bound_path_id
 * @property int type_description_element_id
 * @property bool is_system
 * @property bool is_final
 * @property string ref_uuid
 * @property string type_sum_geom_shape
 * @property string type_name
 * @property TypeOfLifecycle lifecycle
 *
 * @property UserNamespace owner_namespace
 * @property Attribute[] type_attributes
 * @property ElementType[] type_parents
 * @property ElementTypeWhitelist[] type_whitelists
 * @property LocationBound type_map
 * @property TimeBound type_time
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

    protected static function booted(): void
    {
        static::deleting(function (ElementType $type) {


            if ($type->type_time_bound_id) {
                $count_times = ElementType::where('type_time_bound_id',$type->type_time_bound_id)->whereNot('id',$this->id)->count();
                if (!$count_times) {
                    $type->type_time->delete();
                }
            }

            if ($type->type_location_map_bound_id) {
                $count_times = ElementType::where('type_location_map_bound_id',$type->type_location_map_bound_id)->whereNot('id',$this->id)->count();
                if (!$count_times) {
                    $type->type_map->delete();
                }
            }
        });
    }

    public function type_owner() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'owner_namespace_id');
    }

    public function type_map() : BelongsTo {
        return $this->belongsTo(LocationBound::class,'type_location_map_bound_id')
            ->where('location_type',TypeOfLocation::MAP);
    }

    public function type_time() : BelongsTo {
        return $this->belongsTo(TimeBound::class,'type_time_bound_id');
    }



    public function type_attributes() : HasMany {
        return $this->hasMany(Attribute::class,'owner_element_type_id','id');
    }

    public function type_whitelists() : HasMany {
        return $this->hasMany(ElementTypeWhitelist::class,'whitelist_owning_type_id','id');
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
            /** @uses ElementType::type_children(),ElementType::type_parents(),ElementType::type_map(),ElementType::type_time() */
            ->with('type_owner', 'type_attributes', 'type_children', 'type_parents','type_whitelists','type_map','type_time')
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
        if (Thing::where('thing_type_id',$this->id)->where('thing_status',TypeOfThingStatus::THING_PENDING)->count() ) {return true;}

        //and cannot delete if in a path used by a thing
        if (PathPart::buildPath(pending_thing_type_id: $this->id)->exists() ) { return true;}
        return false;
    }

    public function checkCurrentEditAbility() :void {
        if (!$this->owner_namespace->isUserAdmin(Utilities::getCurrentNamespace())) {

            throw new HexbatchNotFound(
                __('msg.type_only_admin_can_edit',['ref'=>$this->getName(),'ns'=>$this->owner_namespace->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::TYPE_CANNOT_EDIT
            );
        }
    }

    public function canNamespaceInherit(UserNamespace $namespace) : bool {
        if (empty($this->type_whitelists)) {return true;}
        if ($this->type_owner->isNamespaceAdmin($namespace)) {return true;}
        return ElementTypeWhitelist::where('whitelist_owning_type_id',$this->id)
            ->where('whitelist_namespace_id',$namespace->id)
            ->where('whitelist_permission',TypeOfWhitelistPermission::INHERITING)->exists();
    }



    public function sumShapeFromAttributes() {
        //then for the attributes that have a shape, do a union of their geometries and store in type_sum_geom_shape
        $id = $this->id;
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


    public static function collectType(Collection $collect, ?Server $parent_server = null) : ElementType {
        try {
            DB::beginTransaction();
            if (is_string($collect) && Utilities::is_uuid($collect)) {
                /**
                 * @var ElementType
                 */
                return (new ElementType())->resolveRouteBinding($collect);
            } else {

                if ($collect->has('uuid')) {
                    $maybe_uuid = $collect->get('uuid');
                    if (is_string($maybe_uuid) &&  Utilities::is_uuid($maybe_uuid) ) {
                        $element_type =  (new ElementType())->resolveRouteBinding($maybe_uuid);
                    } else {

                        throw new HexbatchNotFound(
                            __('msg.type_not_found',['ref'=>(string)$maybe_uuid]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                            RefCodes::TYPE_NOT_FOUND
                        );
                    }
                } else {
                    $element_type = new ElementType();
                    if ($parent_server) {
                        $element_type->imported_from_server_id = $parent_server->id;
                    }
                }

                $element_type->editType($collect);
            }

            DB::commit();
            return $element_type;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof HexbatchCoreException) {
                throw $e;
            }
            throw new HexbatchNotPossibleException(
                $e->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);

        }
    }

    /**
     * @throws \Exception
     */
    public  function editType(Collection $collect) : void {
        try
        {
            DB::beginTransaction();

            if ($collect->has('is_final')) {
                $this->is_final = Utilities::boolishToBool($collect->get('is_final',false));
            }

            if ($collect->has('lifecycle')) {
                $maybe_valid_lifecycle = TypeOfLifecycle::tryFromInput($collect->get('lifecycle'));
                if (in_array($maybe_valid_lifecycle,[TypeOfLifecycle::RETIRED,TypeOfLifecycle::SUSPENDED])) {
                    $this->lifecycle = $maybe_valid_lifecycle;
                }
            }

            if ($collect->has('description_element')) {
                $describe_hint_here = $collect->get('description_element');
                if (!is_string($describe_hint_here) || !Utilities::is_uuid($describe_hint_here)) {
                    throw new HexbatchNotPossibleException(__('msg.type_descriptions_must_be_uuid'),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::TYPE_BAD_SCHEMA);
                }
                /**
                 * @var Element|null $de_element
                 */
                $de_element = (new Element())->resolveRouteBinding($describe_hint_here);
                $this->type_description_element_id = $de_element;
            }

            if (!$this->isInUse()) {


                $this->owner_namespace_id = Utilities::getCurrentNamespace()->id;

                if ($collect->has('parents')) {

                    collect($collect->get('parents'))->each(function ($some_parent_hint, int $key) {
                        Utilities::ignoreVar($key);
                        if (!is_string($some_parent_hint)) {
                            throw new HexbatchNotPossibleException(__('msg.parent_types_must_be_string_names'),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::TYPE_BAD_SCHEMA);
                        }
                        /**
                         * @var ElementType|null $some_parent
                         */
                        $some_parent = (new ElementType())->resolveRouteBinding($some_parent_hint);

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

                try {
                    $this->save();
                } catch (\Exception $f) {
                    throw new HexbatchNotPossibleException(
                        __('msg.type_cannot_be_edited', ['ref' => $this->getName(), 'error' => $f->getMessage()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::TYPE_CANNOT_EDIT);
                }

                collect($collect->get('attributes'))->each(function ($some_attribute_hint, int $key) {
                    Utilities::ignoreVar($key);
                    if ($some_attribute_hint instanceof Collection) {
                        Attribute::collectAttribute(collect: $some_attribute_hint, owner: $this);
                    }
                });

                $this->sumShapeFromAttributes();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
