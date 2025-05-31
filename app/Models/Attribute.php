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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/*
 * if an attribute has a shape, then its value can contain a key for how the shape looks for opacity|border color|fill color|skin url\z-ordering
 * this is not enforced by the code here, but is a well known key for browsers and apps rendering the shape
 * the shape appearance can be adjusted by rules writing to this key
 * if no key for appearance, the browser will use some defaults
 *
 * if attribute needs some protection from read or write using namespaces or other, use rules
 *
 */

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
 * @property bool is_public_domain
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
 * @property ElementType type_owner
 *
 * @property TimeBound attribute_time_bound
 * @property LocationBound attribute_shape_bound
 * @property ServerEvent attached_event
 * @property ElementValue original_element_value
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
        'is_public_domain' => 'boolean',
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

    public function original_element_value() : HasOne {
        return $this->hasOne(ElementValue::class,'horde_attribute_id')
            ->where('horde_type_id',$this->owner_element_type_id)
            ->where('horde_originating_type_id',$this->owner_element_type_id)
            ->whereNull('element_set_member_id');
    }





    public function attribute_shape_bound() : BelongsTo {
        return $this->belongsTo(LocationBound::class,'attribute_location_bound_id')
            ->where('location_type',TypeOfLocation::SHAPE);
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

    public static function getLastNameWithoutType(string $attr_name, bool $verify = true) : string {

        $attribute_parts = explode(static::ATTRIBUTE_FAMILY_SEPERATOR, $attr_name);
        $last_attribute_name_full = $attribute_parts[count($attribute_parts) - 1];
        $parts = explode(UserNamespace::NAMESPACE_SEPERATOR,$last_attribute_name_full);
        if (count($parts) === 1) {
            return $parts[0];
        }
        if ($verify) { static::verifyNameString($attr_name);}
        return $parts[1];
    }


    public static function buildAttribute(
        ?int    $me_id = null,
        ?int    $namespace_id = null,
        ?int    $type_id = null,
        ?int    $shape_id = null,
        ?string $uuid = null,
        bool    $b_do_relations = false
    )
    : Builder
    {
        /** @var Builder $build */
        $build =  Attribute::select('attributes.*')
            ->selectRaw(" extract(epoch from  attributes.created_at) as created_at_ts,  extract(epoch from  attributes.updated_at) as updated_at_ts");

        if ($b_do_relations)
        {
            /** @uses Attribute::attribute_parent(),Attribute::type_owner(),Attribute::attribute_shape_bound() */
            /** @uses Attribute::attached_event(),Attribute::original_element_value() */
            $build->
                with('attribute_parent', 'type_owner', 'attribute_shape_bound', 'attached_event', 'original_element_value');
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

        if ($namespace_id) {


            $build->join('element_types',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($namespace_id) {
                    $join
                        ->on('element_types.id','=','attributes.owner_element_type_id')
                    ->where('owner_namespace_id',$namespace_id);
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

    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed $value
     * @param string|null $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $use_verification = true;
        if ($field === true) {
            $use_verification = false;
            $field = null;
        }
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
                        $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);

                        $what_route =  Route::current();
                        $owner_name = $what_route->originalParameter('element_type');
                        if($owner_name && count($parts) === 1) {
                            $attr_name_raw = $parts[0];
                            $attribute_name = static::getLastNameWithoutType($attr_name_raw,$use_verification);
                            /** @var ElementType $owner */
                            $owner = (new ElementType)->resolveRouteBinding($owner_name);
                            $build = $this->where('owner_element_type_id', $owner?->id)
                                ->where('attribute_name', $attribute_name);
                        }
                        else if (count($parts) === 2) {
                            //here we do not call the helper functions, the resolve attr path will only call this
                            $type_string = $parts[0];
                            $attr_name_raw = $parts[1];
                            $attribute_name = static::getLastNameWithoutType($attr_name_raw,$use_verification);
                            /** @var ElementType $owner */
                            $owner = (new ElementType)->resolveRouteBinding($type_string);
                            $build = $this->where('owner_element_type_id', $owner?->id)->where('attribute_name', $attribute_name);
                        } else if (count($parts) === 3) {
                                $namespace_string = $parts[0];
                                $type_string = $parts[1];
                                $attr_name_raw = $parts[2];
                                $attribute_name = static::getLastNameWithoutType($attr_name_raw,$use_verification);
                                /** @var UserNamespace $da_namespace */
                                $da_namespace = (new UserNamespace())->resolveRouteBinding($namespace_string);

                                /** @var ElementType $owner */
                                $owner = (new ElementType)->resolveRouteBinding($da_namespace->ref_uuid . UserNamespace::NAMESPACE_SEPERATOR . $type_string);
                                $build = $this->where('owner_element_type_id', $owner?->id)->where('attribute_name', $attribute_name);

                        } else if (count($parts) === 4) {
                                $server_string = $parts[0];
                                $namespace_string = $parts[1];
                                $type_string = $parts[2];
                                $attr_name_raw = $parts[3]; //can be split by attribute family separator
                                $attribute_name = static::getLastNameWithoutType($attr_name_raw);


                                /** @var UserNamespace $user_namespace */
                                $user_namespace = (new UserNamespace())->resolveRouteBinding($server_string . UserNamespace::NAMESPACE_SEPERATOR . $namespace_string);

                                /** @var ElementType $owner */
                                $owner = (new ElementType)->resolveRouteBinding($user_namespace->ref_uuid . UserNamespace::NAMESPACE_SEPERATOR . $type_string);

                                $build = $this->where('owner_element_type_id', $owner?->id)->where('attribute_name', $attribute_name);

                        }

                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {

                    $first_build = Attribute::buildAttribute(me_id: $first_id);
                    $ret = $first_build->first();

                }
            }
        }
        catch (\Exception $e) {
            Log::warning('Attribute resolving: '. $e->getMessage());
        }
        finally {
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


}
