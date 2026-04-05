<?php

namespace App\Models;

use App\Enums\Sys\TypeOfEvent;
use App\Enums\Types\TypeOfApproval;
use App\Enums\Types\TypeOfLifecycle;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\IEventReference;
use App\Helpers\Utilities;
use App\Rules\ElementTypeNameReq;
use App\Sys\Res\ISystemModel;
use App\Sys\Res\Types\IType;
use App\Sys\Res\Types\Stk\Root;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
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
 * @property string ref_uuid
 * @property string sum_shape_geom
 * @property string sum_map_geom
 * @property string sum_map_bounding_box
 * @property string sum_shape_bounding_box
 * @property string type_name
 * @property TypeOfLifecycle lifecycle
 *
 * @property UserNamespace owner_namespace
 * @property Server type_server
 * @property Attribute[] type_attributes
 * @property ElementTypeParent[] type_parents
 * @property ElementType[] type_children
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
        'lifecycle'=> TypeOfLifecycle::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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

    public function type_server() : BelongsTo {
        return $this->belongsTo(Server::class,'imported_from_server_id');
    }


    public function type_schedule() : BelongsTo {
        return $this->belongsTo(TimeBound::class,'type_time_bound_id');
    }

    public function type_handle() : BelongsTo {
        return $this->belongsTo(Element::class,'type_handle_element_id');
    }



    public function type_attributes() : HasMany {
        return $this->hasMany(Attribute::class,'owner_element_type_id','id');
    }

    public function type_exposed_attributes() : HasManyThrough {
        return $this->hasManyThrough(
            Attribute::class, //what is returned
            ElementTypeExposedAttribute::class, //the connecting class
            'exposed_type_id', // Foreign key on the connecting table...
            'id', // Foreign key on the returned table...
            'id', // Local key on this class table...
            'exposed_attribute_id' // Local key on the connecting table...
        );
    }

    public function type_ancestors() : HasManyThrough {
        return $this->hasManyThrough(
            ElementType::class, //what is returned
            ElementTypeAncestor::class, //the connecting class
            'owning_child_type_id', // Foreign key on the connecting table...
            'id', // Foreign key on the returned table...
            'id', // Local key on this class table...
            'ancestor_type_id' // Local key on the connecting table...
        );
    }

    public function type_server_levels() : HasMany {
        return $this->hasMany(ElementTypeServerLevel::class,'server_access_type_id','id');
    }

    public function type_children() : HasMany {
        return $this->hasMany(ElementTypeParent::class,'parent_type_id','id')->with('child_type');
    }

    public function type_parents() : HasMany {
        return $this->hasMany(ElementTypeParent::class,'child_type_id','id')
            ->with(['parent_type','parent_type.type_attributes','parent_type.type_attributes.type_owner','parent_type.type_schedule','parent_type.type_parents']);
    }


    public static function getElementType(
        ?int             $id = null,
        ?string          $uuid = null,
        ?int             $owner_namespace_id = null,
        ?string          $type_name = null,
        ?int             $shape_bound_id = null,
        ?int             $time_bound_id = null,
        ?TypeOfLifecycle $lifecycle = null,
    )
    : ElementType
    {
        $ret = static::buildElementType(id:$id,uuid: $uuid, namespace_id: $owner_namespace_id,name: $type_name,
            shape_bound_id: $shape_bound_id,time_bound_id: $time_bound_id, lifecycle: $lifecycle)->first();

        if (!$ret) {
            $arg_types = [];
            $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($type_name) { $arg_types[] = 'name'; $arg_vals[] = $type_name;}
            if ($uuid) { $arg_types[] = 'uuid'; $arg_vals[] = $uuid;}
            if ($owner_namespace_id) { $arg_types[] = 'ns'; $arg_vals[] = $owner_namespace_id;}
            if ($shape_bound_id) { $arg_types[] = 'shape'; $arg_vals[] = $shape_bound_id;}
            if ($time_bound_id) { $arg_types[] = 'time'; $arg_vals[] = $time_bound_id;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new HexbatchNotFound(
                __('msg.type_not_found_by',['types'=>$arg_type,'values'=>$arg_val]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::TYPE_NOT_FOUND
            );
        }
        return $ret;
    }

    public static function buildElementType(
        ?int             $id = null,
        ?string          $uuid = null,
        ?int             $namespace_id = null,
        array            $in_namespace_ids = [],
        ?string          $name = null,
        ?int             $shape_bound_id = null,
        ?int             $time_bound_id = null,
        ?int             $handle_id = null,
        ?bool             $is_system = null,
        ?TypeOfLifecycle $lifecycle = null,
        array            $only_uuids = [],
        bool             $b_child_parent_relations = false,
        bool             $b_server_relations = false,
    )
    : Builder
    {

        $build = ElementType::select('element_types.*')
            ->selectRaw(" extract(epoch from  element_types.created_at) as created_at_ts")
            ->selectRaw("extract(epoch from  element_types.updated_at) as updated_at_ts")

            ->with( 'type_attributes','type_schedule','type_exposed_attributes')
            ;

        if ($b_child_parent_relations) {
            $build->with('type_children', 'type_parents','type_handle');
        }

        if ($b_server_relations) {
            $build->with('owner_namespace', 'type_server', 'type_server_levels');
        }

        if ($id) {
            $build->where('element_types.id', $id);
        }
        if ($namespace_id) {
            $build->where('element_types.owner_namespace_id', $namespace_id);
        }

        if (count($in_namespace_ids)) {
            $build->whereIn('element_types.owner_namespace_id', $in_namespace_ids);
        }

        if ($name) {
            $build->where('element_types.type_name', $name);
        }

        if ($handle_id) {
            $build->where('element_types.type_handle_element_id', $handle_id);
        }
        if ($time_bound_id) {
            $build->where('element_types.type_time_bound_id', $time_bound_id);
        }

        if ($is_system !== null) {
            $build->where('element_types.is_system', $is_system);
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
                        ->where('attributes.attribute_shape_id',$shape_bound_id);
                }
            );
        }

        return $build;
    }


    public static function resolveType(
        ?string $value, ?string $context_namespace_uuid = null, bool $throw_exception = true
    )
    : null|static
    {
        if (!$value) {return null;}
        /** @var Builder $build */
        $build = null;

        if (Utilities::is_uuid($value)) {
            $build = static::buildElementType(uuid: $value);
        } else {

            $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);

            if (count($parts) === 1) {
                if ($context_namespace_uuid) {
                    $owner_hint = $context_namespace_uuid;
                    $maybe_name = $parts[0];
                    /**
                     * @var UserNamespace $owner
                     */
                    $owner = UserNamespace::resolveNamespace($owner_hint);
                    $build = static::buildElementType(namespace_id: $owner->id,name: $maybe_name);
                }

            }

            if (count($parts) === 2) {
                $owner_hint = $parts[0];
                $maybe_name = $parts[1];
                /**
                 * @var UserNamespace $owner
                 */
                $owner = UserNamespace::resolveNamespace($owner_hint);
                $build = static::buildElementType(namespace_id: $owner->id,name: $maybe_name);
            }
        }
        /** @var ElementType|null $ret $ret */
        $ret = $build?->first();

        if (empty($ret) && $throw_exception) {
            throw new HexbatchNotFound(
                __('msg.type_not_found',['ref'=>$value]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::TYPE_NOT_FOUND
            );
        }

        return $ret;
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
        return static::resolveType($value);

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


    public function isPublicDomain() : bool {
        $atts = $this->getAllAttributes();
        if (count($atts) === 0) {return false;}

        foreach ($atts as $att) {
            if (!$att->isPublicDomain()) {return false;}
        }
        return true;
    }

    /**
     * @return Attribute[]
     */
    public function getInheritedAttributes() :array  {
        $attr_hash = [];
        foreach ($this->type_parents as $parent) {
            foreach ($parent->parent_type->getInheritedAttributes() as $att) {
                if ($att->is_abstract) {
                    continue;
                }
                $attr_hash[$att->ref_uuid] = $att;
            }
        }
        return array_values($attr_hash);
    }


    /**
     * @return Attribute[]
     */
    public function getParentUuids() :array  {
        $query_parents = DB::table("element_type_parents as desc_a")
                ->selectRaw('desc_a.id as par_id, 0 as level,desc_a.parent_type_id')->where('desc_a.child_type_id', $this->id)

            ->unionAll(
                DB::table('element_type_parents as desc_b')
                    ->selectRaw('desc_b.id as par_id, level + 1 as level,desc_b.parent_type_id')
                    ->join('query_parents', 'query_parents.parent_type_id', '=', 'desc_b.child_type_id')
            );


        $laravel_parent_uuids = DB::table("element_type_parents")
            ->selectRaw("element_type_parents.id, query_parents.level, parent.ref_uuid as parent_ref_uuid")
            ->join('query_parents', 'query_parents.par_id', '=', 'element_type_parents.id')
            ->join('element_types as parent','parent.id','=','element_type_parents.parent_type_id')
            ->orderBy('level','desc')
            ;

        /** @noinspection PhpUndefinedMethodInspection */
        $laravel_parent_uuids->withRecursiveExpression('query_parents',$query_parents);


        return $laravel_parent_uuids->pluck('parent_ref_uuid')->toArray();
    }

    /**
     * Gets non-abstract attributes
     * @return Attribute[]
     */
    public function getAllAttributes() {

        $attr_hash = $this->getAllAttributeHash();
        foreach ($this->type_attributes as $att) {
            if ($att->is_abstract) {
                continue;
            }
            $attr_hash[$att->ref_uuid] = $att;
        }

        return array_values($attr_hash);
    }

    protected function getAllAttributeHash() : array {
        $attr_hash = [];
        foreach ($this->getInheritedAttributes() as $att) {
            $attr_hash[$att->ref_uuid] = $att;
        }
        return $attr_hash;
    }

    /**
     * @return Attribute[]
     */
    public function getChildlessAbstractAttributes() {
        $all = $this->getAllAttributes();
        $parent_hash = [];
        foreach ( $all as $att) {

            if ($att->attribute_parent) {
                $parent_hash[$att->attribute_parent->ref_uuid] = $att->attribute_parent;
            }
        }

        $fails = [];
        foreach ($all as $att) {
            if ($att->is_abstract) {
                if (!isset($parent_hash[$att->ref_uuid])) {
                    $fails[$att->is_abstract] = $att;
                }
            }
        }

        return array_values($fails);
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




    public static function validateTypeName(string $name,UserNamespace $namespace,?ElementType $me = null) {
        try {
            Validator::make(['type_name' => $name], [
                'type_name' => ['required', 'string', new ElementTypeNameReq(element_type: $me ,current_namespace: $namespace)],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_INVALID_NAME);
        }
    }

    function setTypeName(string $name,? UserNamespace $namespace = null) {
       static::validateTypeName(name:$name,namespace: $this->owner_namespace?: $namespace,me: $this);
       $this->type_name = $name;
    }

    public function hasType(ElementType $element_type) : bool {
        if ($this->getUuid() === $element_type->getUuid()) {return true; } //has itself
        $parent_uuids = $this->getParentUuids();
        return in_array($element_type->getUuid(),$parent_uuids);
    }



    /**
     * @return string[]
     */
    public function getTopParentUuids() : array {
        $ret = [];
        foreach ($this->type_parents as $par) {
            $ret[] = $par->parent_type->ref_uuid;
        }
        return $ret;
    }

    public static function getRootType() : ElementType {
        return ElementType::where('type_name',Root::TYPE_NAME)
            ->where('owner_namespace_id',UserNamespace::getSystemNamespace()->id)
            ->first();
    }

    public function getEventHandlerRef(TypeOfEvent $type_event) : ?IEventReference {
        return null;
    }


}
