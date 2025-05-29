<?php

namespace App\Models;

use App\Enums\Types\TypeOfApproval;
use App\Enums\Types\TypeOfLifecycle;
use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\ElementTypeNameReq;
use App\Sys\Res\ISystemModel;
use App\Sys\Res\Types\IType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


/*
 * can put a path restriction on it be listening to events for set entry
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owner_namespace_id
 * @property int imported_from_server_id
 * @property int type_time_bound_id
 * @property int type_handle_element_id
 * @property bool is_system
 * @property bool is_final_type
 * @property bool is_public_domain
 * @property string ref_uuid
 * @property string sum_shape_geom
 * @property string sum_map_geom
 * @property string sum_map_bounding_box
 * @property string sum_shape_bounding_box
 * @property string type_name
 * @property TypeOfLifecycle lifecycle
 *
 * @property UserNamespace owner_namespace
 * @property Attribute[] type_attributes
 * @property ElementType[] type_parents
 * @property ElementTypeServerLevel[] type_server_levels
 * @property TimeBound type_time
 *
 * @property string created_at
 * @property string updated_at
 *
 */
class ElementType extends Model implements IType,ISystemModel
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
        'is_public_domain' => 'boolean',
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

        });
    }

    public function owner_namespace() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'owner_namespace_id');
    }


    public function type_time() : BelongsTo {
        return $this->belongsTo(TimeBound::class,'type_time_bound_id');
    }



    public function type_attributes() : HasMany {
        return $this->hasMany(Attribute::class,'owner_element_type_id','id');
    }

    public function type_server_levels() : HasMany {
        return $this->hasMany(ElementTypeServerLevel::class,'server_access_type_id','id');
    }

    public function type_children() : HasMany {
        return $this->hasMany(ElementTypeParent::class,'parent_type_id','id');
    }

    public function type_parents() : HasMany {
        return $this->hasMany(ElementTypeParent::class,'child_type_id','id');
    }


    public static function getElementType(
        ?int             $id = null,
        ?string          $uuid = null,
        ?int             $owner_namespace_id = null,
        ?int             $shape_bound_id = null,
        ?int             $time_bound_id = null,
        ?TypeOfLifecycle $lifecycle = null,
    )
    : ElementType
    {
        $ret = static::buildElementType(id:$id,uuid: $uuid,owner_namespace_id: $owner_namespace_id,
            shape_bound_id: $shape_bound_id,time_bound_id: $time_bound_id, lifecycle: $lifecycle)->first();

        if (!$ret) {
            $arg_types = [];
            $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($uuid) { $arg_types[] = 'uuid'; $arg_vals[] = $uuid;}
            if ($owner_namespace_id) { $arg_types[] = 'ns'; $arg_vals[] = $owner_namespace_id;}
            if ($shape_bound_id) { $arg_types[] = 'shape'; $arg_vals[] = $shape_bound_id;}
            if ($time_bound_id) { $arg_types[] = 'time'; $arg_vals[] = $time_bound_id;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new \InvalidArgumentException("Could not find type via $arg_type : $arg_val");
        }
        return $ret;
    }

    public static function buildElementType(
        ?int             $id = null,
        ?string          $uuid = null,
        ?int             $owner_namespace_id = null,
        ?int             $shape_bound_id = null,
        ?int             $time_bound_id = null,
        ?TypeOfLifecycle $lifecycle = null,
        array            $only_uuids = []
    )
    : Builder
    {

        $build = ElementType::select('element_types.*')
            ->selectRaw(" extract(epoch from  element_types.created_at) as created_at_ts,  extract(epoch from  element_types.updated_at) as updated_at_ts")

            /** @uses ElementType::owner_namespace(), ElementType::type_attributes(), ElementType::type_server_levels() */
            /** @uses ElementType::type_children(),ElementType::type_parents(),ElementType::type_time() */
            ->with('owner_namespace', 'type_attributes', 'type_children', 'type_parents','type_server_levels','type_time')
            ;

        if ($id) {
            $build->where('element_types.id', $id);
        }
        if ($owner_namespace_id) {
            $build->where('element_types.owner_namespace_id', $owner_namespace_id);
        }


        if ($time_bound_id) {
            $build->where('element_types.type_time_bound_id', $time_bound_id);
        }

        if ($lifecycle) {
            $build->where('element_types.lifecycle', $lifecycle);
        }

        if ($uuid) {
            $build->where('element_types.ref_uuid', $uuid);
        }

        if (count($only_uuids)) {
            $build->whereIn('element_types.ref_uuid', $only_uuids);
        }

        if ($shape_bound_id) {

            $build->join('attributes',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($shape_bound_id) {
                    $join
                        ->on('element_types.id','=','attributes.owner_element_type_id')
                        ->where('attributes.attribute_location_shape_bound_id',$shape_bound_id);
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
                        $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);
                        if (count($parts) === 2) {
                            $owner_hint = $parts[0];
                            $maybe_name = $parts[1];
                            /**
                             * @var UserNamespace $owner
                             */
                            $owner = (new UserNamespace())->resolveRouteBinding($owner_hint);
                            $build = $this->where('owner_namespace_id', $owner?->id)->where('type_name', $maybe_name);
                        }

                        if (count($parts) === 3) {
                            $server_hint = $parts[0];
                            $namespace_hint = $parts[1];
                            $maybe_name = $parts[2];
                            /**
                             * @var UserNamespace $owner
                             */
                            $owner = (new UserNamespace())->resolveRouteBinding($server_hint.UserNamespace::NAMESPACE_SEPERATOR.$namespace_hint);
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
        return $this->owner_namespace?->getName().UserNamespace::NAMESPACE_SEPERATOR.$this->type_name;
    }

    public function isInUse() : bool {
        if (!$this->id) {return false;}
        if ($this->lifecycle !== TypeOfLifecycle::DEVELOPING) {return true;}
        if (Element::where('element_parent_type_id',$this->id)->exists() ) {return true;}
        if (ElementTypeParent::where('parent_type_id',$this->id)->exists() ) {return true;}

        //and cannot delete if in a path used by a thing
        if (PathPart::buildPathPart(pending_thing_type_id: $this->id)->exists() ) { return true;}
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




    public function sumGeoFromAttributes() {
        //then for the attributes that have a shape, do a union of their geometries and store in sum_shape_geom
        $id = $this->id;
        DB::statement("
            UPDATE element_types
            SET sum_shape_geom=subquery.sum_geo

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

        DB::statement("
            UPDATE element_types
            SET sum_map_geom=subquery.sum_geo

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

            if ($collect->has('is_final_type')) {
                $this->is_final_type = Utilities::boolishToBool($collect->get('is_final_type',false));
            }

            if ($collect->has('lifecycle')) {
                $maybe_valid_lifecycle = TypeOfLifecycle::tryFromInput($collect->get('lifecycle'));
                 if ($this->lifecycle === TypeOfLifecycle::PUBLISHED) {
                     if ($maybe_valid_lifecycle == TypeOfLifecycle::RETIRED) {
                         $this->lifecycle = $maybe_valid_lifecycle;
                     }
                } else if ($this->lifecycle === TypeOfLifecycle::DEVELOPING || $this->lifecycle === TypeOfLifecycle::RETIRED) {
                     if ($maybe_valid_lifecycle === TypeOfLifecycle::PUBLISHED) {
                         //todo if publish, then check for permission for both parent attributes and child types,
                         // required and rules for live, send to things for any listeners on each type used
                         // in the things, those listeners get tied as child things to the publish approval. here, both attributes and types are checked
                         // that means its possible to design stuff using items without permissions (as long as in the proper ns memberships and admins)
                         // the lifecycle will be set to published after all is cleared
                         // bringing back retired types will start a new publish check
                         Utilities::ignoreVar();//linting
                     }
                 }
            }


            if ($collect->has('description_element')) {
                $describe_hint_here = $collect->get('description_element');
                if (!is_string($describe_hint_here) || !Utilities::is_uuid($describe_hint_here)) {
                    throw new HexbatchNotPossibleException(__('msg.type_descriptions_must_be_uuid'),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::TYPE_SCHEMA_ISSUE);
                }
                /**
                 * @var Element|null $de_element
                 */
                $de_element = (new Element())->resolveRouteBinding($describe_hint_here);
                $this->type_handle_element_id = $de_element;
            }

            if (!$this->isInUse()) {


                $this->owner_namespace_id = Utilities::getCurrentNamespace()->id;

                if ($collect->has('parents')) {

                    collect($collect->get('parents'))->each(function ($some_parent_hint, int $key) {
                        Utilities::ignoreVar($key);
                        if (!is_string($some_parent_hint)) {
                            throw new HexbatchNotPossibleException(__('msg.parent_types_must_be_string_names'),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::TYPE_SCHEMA_ISSUE);
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
                                'type_name' => ['required', 'string', new ElementTypeNameReq($this->current_type,Utilities::getCurrentNamespace())],
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

                $owner_namespace = $this->owner_namespace;
                if (!$owner_namespace) {$owner_namespace = UserNamespace::buildNamespace(me_id: $this->owner_namespace_id)->first();}

                if ($collect->has('time_bound')) {
                    $hint_time_bound = $collect->get('time_bound');
                    if (is_string($hint_time_bound) || $hint_time_bound instanceof Collection) {
                        $time_bound = TimeBound::collectTimeBound(collect: $hint_time_bound,namespace: $owner_namespace);
                        $this->type_time_bound_id = $time_bound->id;
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

                $this->sumGeoFromAttributes();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function checkInUse() {
        if ($this->isInUse()) {

            throw new HexbatchPermissionException(__("msg.type_in_use",['ref'=>$this->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::RULE_CANNOT_DELETE);
        }
    }


    public function getTypeObject(): ?ElementType {
        return $this;
    }

    public function getUuid(): string{
        return $this->ref_uuid;
    }

    public function canBePublished() : bool {
        if ($this->lifecycle !== TypeOfLifecycle::DEVELOPING) {return false;}
        foreach ($this->type_parents as $parent) {
            if ($parent->parent_type_approval !== TypeOfApproval::DESIGN_APPROVED) {return false;}
        }

        foreach ($this->type_attributes as $att) {
            if ($att->attribute_approval !== TypeOfApproval::DESIGN_APPROVED) {return false;}
        }

        return true;
    }

    public function isPublished() : bool {
        return $this->lifecycle === TypeOfLifecycle::PUBLISHED;
    }

    /**
     * @return string[]
     */
    public function getParentUuids() : array {
        $ret = [];
        foreach ($this->type_parents as $par) {
            $ret[] = $par->ref_uuid;
        }
        return $ret;
    }

    public function isParentOfThis(ElementType $type) {
        foreach ($this->type_parents as $par) {
            if ($type->ref_uuid === $par->ref_uuid) {return true;}
        }
        return false;
    }


}
