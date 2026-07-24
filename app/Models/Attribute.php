<?php

namespace App\Models;

use App\Data\ApiParams\Rules\ValidateNamespaceRef;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Data\ApiParams\Enums\TypeOfLocation;
use App\Enums\Sys\TypeOfEvent;
use App\Enums\Types\TypeOfApproval;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Events\IEventReference;
use App\Helpers\Utilities;
use App\Rules\AttributeNameReq;
use App\Sys\Build\NewBuild;
use App\Sys\Res\ISystemModel;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owner_element_type_id
 * @property int parent_attribute_id
 * @property int design_attribute_id
 * @property int attribute_shape_id
 * @property bool is_system
 * @property bool is_final_attribute
 * @property bool is_abstract
 * @property TypeOfServerAccess access_policy
 * @property string ref_uuid
 * @property string read_json_path
 * @property string validate_json_path
 * @property ArrayObject attribute_default_value
 * @property string attribute_name
 *
 * @property TypeOfElementValuePolicy value_policy
 * @property TypeOfApproval attribute_approval
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 *
 * @property Attribute attribute_parent
 * @property Attribute attribute_design
 * @property ElementType type_owner
 *
 * @property LocationBound attribute_location
 * @property ServerEvent attached_event
 */
class Attribute extends Model implements ISystemModel
{

    protected $table = 'attributes';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

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
        'is_system' => 'boolean',
        'is_final_attribute' => 'boolean',
        'is_abstract' => 'boolean',
        'access_policy' => TypeOfServerAccess::class,
        'value_policy' => TypeOfElementValuePolicy::class,
        'attribute_approval' => TypeOfApproval::class,
        'attribute_default_value' => AsArrayObject::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];




    public function attribute_parent() : BelongsTo {
        return $this->belongsTo(Attribute::class,'parent_attribute_id')
            ->with('attribute_parent')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts");

    }


    public function type_owner() : BelongsTo {
        return $this->belongsTo(ElementType::class,'owner_element_type_id');
    }

    public function attached_event() : HasOne {
        return $this->hasOne(ServerEvent::class,'owning_attribute_id')
            /** @uses ServerEvent::top_rule() */
            ->with('top_rule');
    }



    public function attribute_ancestors() : HasManyThrough {
        return $this->hasManyThrough(
            Attribute::class, //what is returned
            AttributeAncestor::class, //the connecting class
            'child_attribute_id', // Foreign key on the connecting table...
            'id', // Foreign key on the returned table...
            'id', // Local key on this class table...
            'ancestor_attribute_id' // Local key on the connecting table...
        );
    }






    public function attribute_location() : BelongsTo {
        return $this->belongsTo(LocationBound::class,'attribute_location_bound_id')
            ->where('location_type',TypeOfLocation::SHAPE);
    }

    public function attribute_design() : BelongsTo {
        return $this->belongsTo(Attribute::class,'design_attribute_id');
    }

    const ATTRIBUTE_FAMILY_SEPERATOR = '\\';


    public function getName(bool $short_name = true) : string  {

        if ($short_name) {
            return $this->type_owner->getName() .  ValidateNamespaceRef::NAMESPACE_SEPERATOR . $this->attribute_name;
        }
        //get ancestor chain
        $names = [];
        $parent = $this->attribute_parent;
        while ($parent) {
            $names[] = $parent->getName();
            $parent = $parent->attribute_parent;

        }
        if (empty($names)) {
            return $this->getName();
        }

        return  implode(static::ATTRIBUTE_FAMILY_SEPERATOR,$names);
    }




    public static function buildAttribute(
        ?int    $me_id = null,
        ?int    $namespace_id = null,
        ?int    $member_of_namespace_id = null,
        ?int    $parent_id = null,
        array   $in_namespace_ids = [],
        array   $in_type_ids = [],
        ?int    $type_id = null,
        ?int    $shape_id = null,
        ?int    $design_id = null,
        ?string $uuid = null,
        ?bool   $is_system = null,
        bool    $b_do_relations = false,
        bool    $b_do_ancestors = false,
        ?string $name = null
    )
    : Builder
    {
        /** @var Builder $build */
        $build =  Attribute::select('attributes.*')
            ->selectRaw(" extract(epoch from  attributes.created_at) as created_at_ts")
            ->selectRaw("  extract(epoch from  attributes.updated_at) as updated_at_ts");

        if ($b_do_relations)
        {
            /** @uses Attribute::attribute_parent(),Attribute::type_owner(),Attribute::attribute_location() */
            /** @uses Attribute::attached_event(),Attribute::attribute_design() */
            $build->
                with('attribute_parent', 'type_owner', 'attribute_location', 'attached_event','attribute_design');
        }

        if ($b_do_ancestors)
        {
            /** @uses Attribute::attribute_ancestors() */
            $build->with('attribute_ancestors');
        }


        if ($me_id) {
            $build->where('attributes.id',$me_id);
        }

        if ($parent_id) {
            $build->where('attributes.parent_attribute_id',$me_id);
        }

        if ($design_id) {
            $build->where('attributes.design_attribute_id',$design_id);
        }

        if ($type_id) {
            $build->where('attributes.owner_element_type_id',$type_id);
        }

        if (count($in_type_ids)) {
            $build->whereIn('attributes.owner_element_type_id',$in_type_ids);
        }

        if ($uuid) {
            $build->where('attributes.ref_uuid',$uuid);
        }

        if ($shape_id) {
            $build->where('attributes.attribute_shape_id',$shape_id);
        }

        if ($name) {
            $build->where('attributes.attribute_name',$name);
        }

        if ($is_system !== null ) {
            $build->where('attributes.is_system',$is_system);
        }

        if ($namespace_id) {


            $build->join('element_types ots',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($namespace_id) {
                    $join
                        ->on('ots.id','=','attributes.owner_element_type_id')
                    ->where('ots.owner_namespace_id',$namespace_id);
                }
            );
        }

        if (count($in_namespace_ids)) {


            $build->join('element_types as ets',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($in_namespace_ids) {
                    $join
                        ->on('ets.id','=','attributes.owner_element_type_id')
                    ->whereIn('ets.owner_namespace_id',$in_namespace_ids);
                }
            );
        }

        if ($member_of_namespace_id) {

            $build->join('element_types otm',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($namespace_id) {
                    $join
                        ->on('otm.id','=','attributes.owner_element_type_id')
                        ->where('otm.owner_namespace_id',$namespace_id);
                }
            );

            $build->join('user_namespace_members as ms',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use ($member_of_namespace_id) {
                    $join
                        ->on('otm.owner_namespace_id', '=', 'ms.parent_namespace_id')
                        ->where('ms.member_namespace_id', $member_of_namespace_id);
                }
            );
        }



        return $build;
    }

    public static function getThisAttribute(
        ?int             $id = null,
        ?string          $uuid = null,
        bool        $b_do_relations = false
    )
    : Attribute
    {
        $ret = static::buildAttribute(me_id:$id,uuid: $uuid,b_do_relations: $b_do_relations)->first();

        if (!$ret) {
            $arg_types = []; $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($uuid) { $arg_types[] = 'uuid'; $arg_vals[] = $uuid;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new HexbatchNotFound(
                __('msg.attribute_not_found_by',['types'=>$arg_type,'values'=>$arg_val]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::ATTRIBUTE_NOT_FOUND
            );
        }
        return $ret;
    }

    public static function resolveAttribute(?string $value, bool $throw_exception = true, bool $do_relations = false)
    : ?static
    {

         if (!$value) {return null;}
        /** @var Builder $build */
        $build = null;

        if (Utilities::is_uuid($value)) {
            $build = static::buildAttribute(uuid: $value,b_do_relations: $do_relations);
        } else {

            $parts = explode(ValidateNamespaceRef::NAMESPACE_SEPERATOR, $value);
            if (count($parts) === 2) {
                $type_hint = $parts[0];
                $attr_name = $parts[1];
                /**
                 * @var UserNamespace $owner
                 */
                $owner = ElementType::resolveType($type_hint);
                $build = static::buildAttribute(type_id: $owner->id, b_do_relations: $do_relations, name: $attr_name);
            } else if (count($parts) === 3) {
                $namespace_hint = $parts[0];
                $type_hint = $parts[1];
                $attr_name = $parts[2];
                $owner = ElementType::resolveType($namespace_hint . ValidateNamespaceRef::NAMESPACE_SEPERATOR.$type_hint);
                $build = static::buildAttribute(type_id: $owner->id, b_do_relations: $do_relations,name: $attr_name);
            }

        }

        /** @var Attribute|null $ret */
        $ret = $build?->first();

        if (empty($ret) && $throw_exception) {
            throw new HexbatchNotFound(
                __('msg.attribute_not_found',['ref'=>$value]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::ATTRIBUTE_NOT_FOUND
            );
        }

        return $ret;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed $value
     * @param string|null $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return static::resolveAttribute($value);
    }


    /**
     * @param int $skip_first_number_ancestors set to 1 to also show direct parent, 2 to not show the grandparent
     * @return array
     */
    public function getAncestorChain(int $skip_first_number_ancestors = 1) {
        if ($skip_first_number_ancestors < 1) {$skip_first_number_ancestors = 1;}
       $ancestors = [];
       $current = $this;
       while($parent = $current->attribute_parent) {
           $current = $parent;
           $ancestors[] = $parent;

       }

       for($i = 0; $i < $skip_first_number_ancestors; $i++) {
           array_shift($ancestors);
       }
       $out = array_reverse($ancestors);
       return $out;

    }

    public function checkAttributeOwnership(ElementType $owner) {
        if ($this->id && $this->owner_element_type_id !== $owner->id) {

            throw new HexbatchNotFound(
                __('msg.attribute_owner_does_not_match_type_given',['ref'=>$this->getName(),'type'=>$owner->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::TYPE_CANNOT_EDIT
            );
        }
    }



    public function checkRuleOwnership(AttributeRule $rule) {
        if ($this->id && $this->attached_event->id !== $rule->owning_server_event_id) {

            throw new HexbatchNotFound(
                __('msg.rule_owner_does_not_match_attribute_given',['ref'=>$rule->getName(),'attribute'=>$this->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::RULE_NOT_FOUND
            );
        }
    }



    public function getUuid(): string {
        return $this->ref_uuid;
    }

    function setAttributeName(string $name) {
        try {
            Validator::make(['attribute_name' => $name], [
                'attribute_name' => ['required', 'string', new AttributeNameReq(element_type_id: $this->owner_element_type_id,attribute: $this)],
            ])->validate();
            $this->attribute_name = $name;
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }
    }



    public static function getAttributeIdsFromInput(
        array $references,
        ?string $default_type = null,?string $default_ns = null,?string $default_server = null,
        bool $b_allow_type_ids = true
    ) : array
    {
        $ret = ['ids'=>[],'uuids'=>[],'names'=>[],'type_uuids'=>[],'type_names'=>[],];
        $adjusted_refs = [];

        if (!$default_server) {$default_server = config('hbc.system.server.uuid');}

        foreach ($references as $ref) {
            if (!trim($ref)) {continue;}

            $parts = explode(ValidateNamespaceRef::NAMESPACE_SEPERATOR,$ref);
            if (count($parts) > 4) {
                continue;
            }

            $outs = [];

            $my_id = trim($parts[0]??'');

            if (count($parts) === 1 &&  ctype_digit($my_id) )
            {
                if (!$b_allow_type_ids) { continue;}
                $outs = [(int)$my_id];
            }
            else if (count($parts) === 1 &&  Utilities::is_uuid($my_id) ) {
                $outs = [$my_id];
            }
            else
            {
                if (count($parts) === 4) {
                    $att_ref = trim($parts[3]??'');
                    $type_ref = trim($parts[2]??'');
                    $ns_ref = trim($parts[1]??'');
                    $server_ref = trim($parts[0]??'');
                }
                else if (count($parts) === 3) {
                    $att_ref = trim($parts[2]??'');
                    $type_ref = trim($parts[1]??'');
                    $ns_ref = trim($parts[0]??'');
                    $server_ref = null;
                } elseif (count($parts) === 2) {
                    $att_ref = trim($parts[2]??'');
                    $type_ref = trim($parts[1]??'');
                    $ns_ref = null;
                    $server_ref = null;
                } elseif (count($parts) === 1) {
                    $att_ref = trim($parts[0]??'');
                    $type_ref = null;
                    $ns_ref = null;
                    $server_ref = null;
                } else {
                    throw new \LogicException("should never get to the count > 4 or < 1");
                }
                if (!$att_ref) { continue;}
                if (!$type_ref && !$default_type) { continue;}
                if (!$ns_ref && !$default_ns) { continue;}

                if (!$type_ref) { $type_ref = $default_type;}
                if (!$ns_ref) { $ns_ref = $default_ns;}
                if (!$server_ref) { $server_ref = $default_server;}

                $outs[] = $server_ref;
                $outs[] = $ns_ref;
                $outs[] = $type_ref;
                $outs[] = $att_ref;
            }

            $adjusted_refs[] = implode(ValidateNamespaceRef::NAMESPACE_SEPERATOR,$outs);

        }

        if (!count($adjusted_refs)) {return $ret;}

        $values_array = [];
        foreach ($adjusted_refs as $ad) {
            Utilities::ignoreVar($ad);
            $values_array[] = "(?)";
        }

        $values = implode(",\n",$values_array);

        $separator = ValidateNamespaceRef::NAMESPACE_SEPERATOR;

        $sql = "
            WITH
            raw_inputs as
                (SELECT da_input
                 FROM (VALUES
                           $values
                       ) AS q (da_input))
            SELECT a.id, a.attribute_name, a.ref_uuid,t.type_name, t.ref_uuid as type_ref
                FROM attributes a
                CROSS JOIN raw_inputs
                INNER JOIN element_types t on t.id = a.owner_element_type_id
                INNER JOIN user_namespaces u on u.id = t.owner_namespace_id
                INNER JOIN servers s on s.id = u.namespace_server_id
                WHERE
                (
                    split_part(raw_inputs.da_input, '$separator', 4) = ''
                    AND
                    split_part(raw_inputs.da_input, '$separator', 3) = ''
                    AND
                    split_part(raw_inputs.da_input, '$separator', 2) = ''
                    AND
                    (
                        a.id::bigint = bigint_or_null(split_part(raw_inputs.da_input, '$separator', 1))
                        OR
                        a.ref_uuid = uuid_or_null(split_part(raw_inputs.da_input, '$separator', 1))
                    )
                )
                OR
                (
                    (
                        -- match attribute uuid or name

                        a.attribute_name = split_part(raw_inputs.da_input, '$separator', 4)::text
                            OR
                        a.ref_uuid = uuid_or_null(split_part(raw_inputs.da_input, '$separator', 4))

                    )
                    AND
                    (
                        -- match type uuid or name

                        t.type_name = split_part(raw_inputs.da_input, '$separator', 3)::text
                            OR
                        t.ref_uuid = uuid_or_null(split_part(raw_inputs.da_input, '$separator', 3))

                    )
                    AND
                    (
                        -- match namespace uuid or name

                        u.ref_uuid = uuid_or_null(split_part(raw_inputs.da_input, '$separator', 2))
                            OR
                        u.namespace_name = split_part(raw_inputs.da_input, '$separator', 2)::text
                    )
                    AND
                    (
                        -- match server uuid or name

                        s.ref_uuid = uuid_or_null(split_part(raw_inputs.da_input, '$separator', 1))
                            OR
                        s.server_name = split_part(raw_inputs.da_input, '$separator', 1)::text
                    )
                )
        ;
        ";

        $what = DB::select($sql,$adjusted_refs);


        foreach ($what as $row) {
            $ret['ids'][] = $row->id;
            $ret['uuids'][] = $row->ref_uuid;
            $ret['names'][] = $row->attribute_name;
            $ret['type_uuids'][] = $row->type_ref;
            $ret['type_names'][] = $row->type_name;
        }
        return $ret;

    }


    public function checkDataValidation(?array $data)  {
        if ($data && $this->validate_json_path) {
            $b_ok_val = DB::selectOne("SELECT jsonb_path_exists(:jsonb_data, :json_path) as da_validation",
                ['jsonb_data'=>$data,'json_path'=>$this->validate_json_path])->da_validation;

            if (!$b_ok_val) {
                throw new HexbatchNotPossibleException(
                    __('attribute_validation_failed',['ref'=>$this->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }
        }
    }

    public function setDefaultValue(array $default_value) : void
    {
        //must pass write validation and return something in the read
        $b_ok_val = true;
        if ($this->validate_json_path) {
            $b_ok_val = DB::selectOne("SELECT jsonb_path_exists(:jsonb_data, :json_path) as da_validation",
                ['jsonb_data'=>$default_value,'json_path'=>$this->validate_json_path])->da_validation;
        }

        $b_ok_read = true;
        if ($this->read_json_path) {
            $b_ok_read = DB::selectOne("SELECT jsonb_path_exists(:jsonb_data, :json_path) as da_read",
                ['jsonb_data'=>$default_value,'json_path'=>$this->read_json_path])->da_read;
        }

       if (!$b_ok_val && !$b_ok_read) {
            $msg = 'attribute_has_invalid_default';
        } else if(!$b_ok_val) {
            $msg = 'attribute_has_invalid_default_validation';
        }
        else if(!$b_ok_read) {
            $msg = 'attribute_has_invalid_default_read';
        } else {
            $this->attribute_default_value = $default_value;
            return;
        }

        throw new HexbatchNotPossibleException(
            __($msg,['ref'=>$this->getName()]),
            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);

    }

    public function isPublicDomain() {
        return $this->access_policy === TypeOfServerAccess::IS_PUBLIC_DOMAIN;
    }

    public function getNotesAttribute(): ?string
    {
        $class = NewBuild::getClassFromUuid(uuid: $this->ref_uuid);
        return $class::getHexbatchDescriptionMarkdown();
    }

    public function getBlurbAttribute(): ?string
    {
        $class = NewBuild::getClassFromUuid(uuid: $this->ref_uuid);
        return $class::getHexbatchBlurb();
    }


    /**
     * @return Collection<IEventReference>
     */
    public static function getEventHandlerRefsFromAttributes(TypeOfEvent $event_type,array $attribute_ids) : Collection {
        Utilities::ignoreVar($event_type,$attribute_ids);
        return new Collection;
    }
}
