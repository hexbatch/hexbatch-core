<?php

namespace App\Models;

use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Enums\Bounds\TypeOfLocation;
use App\Enums\Types\TypeOfApproval;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\AttributeNameReq;
use App\Sys\Res\Atr\IAttribute;
use App\Sys\Res\ISystemModel;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\JoinClause;
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
 * @property TypeOfServerAccess server_access_type
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
 * @property TimeBound attribute_time_bound
 * @property LocationBound attribute_shape_bound
 * @property ServerEvent attached_event
 */
class Attribute extends Model implements IAttribute,ISystemModel
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
        'server_access_type' => TypeOfServerAccess::class,
        'value_policy' => TypeOfElementValuePolicy::class,
        'attribute_approval' => TypeOfApproval::class,
        'attribute_default_value' => AsArrayObject::class,
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






    public function attribute_shape_bound() : BelongsTo {
        return $this->belongsTo(LocationBound::class,'attribute_location_bound_id')
            ->where('location_type',TypeOfLocation::SHAPE);
    }

    public function attribute_design() : BelongsTo {
        return $this->belongsTo(Attribute::class,'design_attribute_id');
    }

    const ATTRIBUTE_FAMILY_SEPERATOR = '\\';


    public function getName(bool $short_name = true) : string  {

        if ($short_name) {
            return $this->type_owner->getName() .  UserNamespace::NAMESPACE_SEPERATOR . $this->attribute_name;
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


    public static function verifyNameString(string $attr_name) : void {
        $attribute_parts = explode(static::ATTRIBUTE_FAMILY_SEPERATOR, $attr_name);
        $children_first = array_reverse($attribute_parts);

        /**
         * @var Attribute $next_parent
         */
        $next_parent = null;
        $bad = false;
        $count = 0;
        foreach ($children_first as $attr) {

            /**
             * @var Attribute $node
             */
            $node = (new Attribute())->resolveRouteBinding($attr,true);
            if ($next_parent && $node->attribute_parent->ref_uuid !== $next_parent->ref_uuid) {
                //next parent is not the parent of this node
                $bad = true; break;
            }
            $next_parent = $node->attribute_parent; //this should be the next node, compare to the next child
            $count++;
            if (!$next_parent && $count < count($children_first)) {
                //no parent before the end,node has no parent but not finished with string
                $bad = true; break;
            }
        }

        if ($bad) {
            throw new HexbatchNotFound(
                __('msg.attribute_not_found',['ref'=>$attr_name]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::ATTRIBUTE_NOT_FOUND
            );
        }
    }


    public static function buildAttribute(
        ?int    $me_id = null,
        ?int    $namespace_id = null,
        array    $in_namespace_ids = [],
        ?int    $type_id = null,
        ?int    $shape_id = null,
        ?string $uuid = null,
        bool    $b_do_relations = false,
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
            /** @uses Attribute::attribute_parent(),Attribute::type_owner(),Attribute::attribute_shape_bound() */
            /** @uses Attribute::attached_event() */
            $build->
                with('attribute_parent', 'type_owner', 'attribute_shape_bound', 'attached_event');
        }


        if ($me_id) {
            $build->where('attributes.id',$me_id);
        }

        if ($type_id) {
            $build->where('attributes.owner_element_type_id',$type_id);
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



        return $build;
    }

    public static function getThisAttribute(
        ?int             $id = null,
        ?string          $uuid = null
    )
    : Attribute
    {
        $ret = static::buildAttribute(me_id:$id,uuid: $uuid)->first();

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

    public static function resolveAttribute(string $value, bool $throw_exception = true)
    : static
    {

        /** @var Builder $build */
        $build = null;

        if (Utilities::is_uuid($value)) {
            $build = static::buildAttribute(uuid: $value);
        } else {

            $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);
            if (count($parts) === 2) {
                $type_hint = $parts[0];
                $attr_name = $parts[1];
                /**
                 * @var UserNamespace $owner
                 */
                $owner = ElementType::resolveType($type_hint);
                $build = static::buildAttribute(type_id: $owner->id,name: $attr_name);
            }

            if (count($parts) === 3) {
                $namespace_hint = $parts[0];
                $type_hint = $parts[1];
                $attr_name = $parts[2];
                $owner = ElementType::resolveType($namespace_hint . UserNamespace::NAMESPACE_SEPERATOR.$type_hint);
                $build = static::buildAttribute(type_id: $owner->id,name: $attr_name);
            }

        }

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




    public function getAttributeObject() : ?Attribute {
        return $this;
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

    public function checkValidation(?array $data)  {
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
        return $this->server_access_type === TypeOfServerAccess::IS_PUBLIC_DOMAIN;
    }


}
