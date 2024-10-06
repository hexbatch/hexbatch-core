<?php

namespace App\Models;

use App\Enums\Attributes\TypeOfAttributeServerAccess;
use App\Enums\Attributes\TypeOfAttributeAccess;
use App\Enums\Attributes\TypeOfEncryptionPolicy;
use App\Enums\Attributes\TypeOfSetValuePolicy;
use App\Enums\Bounds\TypeOfLocation;
use App\Enums\Rules\TypeMergeJson;
use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\AttributeNameReq;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owner_element_type_id
 * @property int parent_attribute_id
 * @property int design_attribute_id
 * @property int attribute_location_shape_bound_id
 * @property bool is_retired
 * @property bool is_final_parent
 * @property bool is_system
 * @property bool is_final
 * @property TypeOfAttributeServerAccess server_access_type
 * @property TypeOfAttributeAccess attribute_access_type
 * @property string ref_uuid
 * @property TypeMergeJson popped_writing_method
 * @property TypeMergeJson live_merge_method
 * @property TypeMergeJson reentry_merge_method
 * @property TypeOfEncryptionPolicy encryption_policy
 * @property TypeOfSetValuePolicy set_value_policy
 * @property ArrayObject attribute_value
 * @property ArrayObject attribute_shape_setting
 * @property string value_json_path
 * @property string attribute_name
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
 * @property AttributeRule top_rule
 */
class Attribute extends Model
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
        'attribute_value' => AsArrayObject::class,
        'attribute_shape_setting' => AsArrayObject::class,
        'server_access_type' => TypeOfAttributeServerAccess::class,
        'attribute_access_type' => TypeOfAttributeAccess::class,
        'popped_writing_method' => TypeMergeJson::class,
        'live_merge_method' => TypeMergeJson::class,
        'reentry_merge_method' => TypeMergeJson::class,
        'encryption_policy' => TypeOfEncryptionPolicy::class,
        'set_value_policy' => TypeOfSetValuePolicy::class,
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

    public function top_rule() : BelongsTo {
        return $this->belongsTo(AttributeRule::class,'owning_attribute_id');
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
        ?int $id = null,
        ?int $namespace_id = null,
        ?int $element_type_id = null
    )
    : Builder
    {

        $build =  Attribute::select('attributes.*')
            ->selectRaw(" extract(epoch from  attributes.created_at) as created_at_ts,  extract(epoch from  attributes.updated_at) as updated_at_ts")
            /** @uses Attribute::attribute_parent(),Attribute::type_owner(),Attribute::attribute_shape_bound(),Attribute::top_rule() */
            ->with('attribute_parent', 'type_owner','attribute_shape_bound','top_rule')


       ;

        if ($id) {
            $build->where('attributes.id',$id);
        }

        if ($element_type_id) {
            $build->where('attributes.owner_element_type_id',$element_type_id);
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

                    $first_build = Attribute::buildAttribute(id: $first_id);
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


    public function getValue() {
        return $this->attribute_value;
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


    public static function collectAttribute(Collection|string $collect,ElementType $owner,?Attribute $attribute = null ) : Attribute {


        try {
            DB::beginTransaction();
            if (is_string($collect) && Utilities::is_uuid($collect)) {
                /**
                 * @var Attribute
                 */
                return (new Attribute())->resolveRouteBinding($collect);
            } else {
                $owner->checkCurrentEditAbility();
                if(!$attribute) {
                    if ($collect->has('uuid')) {
                        $maybe_uuid = $collect->get('uuid');
                        if (is_string($maybe_uuid) && Utilities::is_uuid($maybe_uuid)) {
                            /** @var Attribute $attribute */
                            $attribute = (new Attribute())->resolveRouteBinding($maybe_uuid);
                            if ($attribute->type_owner->ref_uuid !== $owner->ref_uuid) {
                                throw new \LogicException("Mismatch of attribute owner and passed in owner ");
                            }
                        } else {

                            throw new HexbatchNotFound(
                                __('msg.attribute_not_found', ['ref' => (string)$maybe_uuid]),
                                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                                RefCodes::ATTRIBUTE_NOT_FOUND
                            );
                        }
                    }
                } else {
                    $attribute = new Attribute();
                }

                $attribute->editAttribute($collect,$owner);
            }

            DB::commit();
            return $attribute;
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
    public function editAttribute(Collection $collect, ElementType $owner) : void {
        $this->checkAttributeOwnership($owner);
        try {

            DB::beginTransaction();


            if ($collect->has('shape_setting')) {
                $this->attribute_shape_setting = $collect->get('shape_settings');
                if (empty($this->attribute_shape_setting)) { $this->attribute_shape_setting = null;}
            }



            if ($collect->has('is_retired')) {
                $this->is_retired = Utilities::boolishToBool($collect->get('is_retired',false));
            }

            if ($collect->has('is_final_parent')) {
                $this->is_final_parent = Utilities::boolishToBool($collect->get('is_final_parent',false));
            }

            if ($collect->has('is_final')) {
                $this->is_final = Utilities::boolishToBool($collect->get('is_final',false));
            }


            if (!$owner->isInUse()) {

                $this->owner_element_type_id = $owner->id;

                if ($collect->has('attribute_name')) {
                    $this->attribute_name = $collect->get('attribute_name');
                    try {
                        Validator::make(['attribute_name' => $this->attribute_name], [
                            'attribute_name' => ['required', 'string', new AttributeNameReq($this->parent_type,$this->current_attribute)],
                        ])->validate();
                    } catch (ValidationException $v) {
                        throw new HexbatchNotPossibleException($v->getMessage(),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_BAD_NAME);
                    }
                }

                if (!$this->attribute_name) {

                    throw new HexbatchNotPossibleException(__('msg.attribute_schema_must_have_name'),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_BAD_NAME);
                }


                if ($collect->has('parent')) {
                    $maybe_uuid = $collect->get('parent');
                    if (is_string($maybe_uuid)) {
                        $parent_attribute = (new Attribute())->resolveRouteBinding($maybe_uuid);
                        /** @var Attribute $parent_attribute */
                        if (!$parent_attribute->type_owner->owner_namespace->isNamespaceAdmin(Utilities::getCurrentNamespace())) {
                            throw new HexbatchNotPossibleException(
                                __('msg.attribute_cannot_be_used_as_parent', ['ref' => $parent_attribute->getName()]),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                        }

                        if ($parent_attribute->is_retired || $parent_attribute->is_final_parent) {
                            throw new HexbatchNotPossibleException(
                                __('msg.attribute_cannot_be_used_at_parent_final', ['ref' => $parent_attribute->getName()]),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                        }
                        $this->parent_attribute_id = $parent_attribute->id;

                    } else {
                        throw new HexbatchNotPossibleException(
                            __('msg.attribute_parent_not_found', ['ref' => $maybe_uuid]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                }


                if ($collect->has('shape')) {
                    $hint_location_bound = $collect->get('shape');
                    if (is_string($hint_location_bound) || $hint_location_bound instanceof Collection) {
                        $bound = LocationBound::collectLocationBound($hint_location_bound);
                        if ($bound->location_type === TypeOfLocation::MAP) {
                            throw new HexbatchNotPossibleException(__('msg.attribute_cannot_use_map',['ref'=>$this->getName()]),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::TYPE_BAD_SCHEMA);
                        }
                        $this->attribute_location_shape_bound_id = $bound->id;
                    }
                }

                if ($collect->has('design')) {
                    $hint_design = $collect->get('design');
                    if (is_string($hint_design) || Utilities::is_uuid($hint_design)) {
                        /** @var Attribute $design */
                        $design = (new Attribute())->resolveRouteBinding($hint_design);
                        if (!$design->type_owner->owner_namespace->isNamespaceMember(Utilities::getCurrentNamespace()) || $design->is_retired) {

                            throw new HexbatchNotPossibleException(__('msg.attribute_cannot_use_design',['ref'=>$design->getName(),'me'=>$this->attribute_name]),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::TYPE_BAD_SCHEMA);
                        }
                        $this->design_attribute_id = $design->id;
                    }
                }

                if ($collect->has('value_json_path')) {
                    $this->value_json_path = $collect->get('value_json_path');
                    Utilities::testValidJsonPath($this->value_json_path);
                }

                if ($collect->has('attribute_value')) {
                    $this->attribute_value = $collect->get('attribute_value');
                }


                if ($collect->has('encryption_policy')) {
                    $this->encryption_policy = TypeOfEncryptionPolicy::tryFromInput($collect->get('encryption_policy'));
                }

                if ($collect->has('attribute_access_type')) {
                    $this->attribute_access_type = TypeOfAttributeAccess::tryFromInput($collect->get('attribute_access_type'));
                }

                if ($collect->has('server_access_type')) {
                    $this->server_access_type = TypeOfAttributeServerAccess::tryFromInput($collect->get('server_access_type'));
                }



                if ($collect->has('set_value_policy')) {
                    $this->set_value_policy = TypeOfSetValuePolicy::tryFromInput($collect->get('set_value_policy'));
                }

                if ($collect->has('popped_writing_method')) {
                    $this->popped_writing_method = TypeMergeJson::tryFromInput($collect->get('popped_writing_method'));
                }

                if ($collect->has('reentry_merge_method')) {
                    $this->reentry_merge_method = TypeMergeJson::tryFromInput($collect->get('reentry_merge_method'));
                }

                if ($collect->has('live_merge_method')) {
                    $this->live_merge_method = TypeMergeJson::tryFromInput($collect->get('live_merge_method'));
                }

            }

            try {
                $this->save();
            } catch (\Exception $f) {
                throw new HexbatchNotPossibleException(
                    __('msg.attribute_cannot_be_edited',['ref'=>$this->getName(),'error'=>$f->getMessage()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }


            DB::commit();


        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


}
