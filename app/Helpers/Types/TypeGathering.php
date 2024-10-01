<?php

namespace App\Helpers\Types;

use App\Enums\Attributes\AttributePingType;

use App\Exceptions\HexbatchNotPossibleException;

use App\Exceptions\RefCodes;
use App\Helpers\Attributes\AttributeGathering;
use App\Helpers\Utilities;

use App\Models\ElementType;
use App\Models\ElementTypeHorde;
use App\Models\ElementTypeParent;
use App\Models\User;
use App\Rules\ElementTypeNameReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TypeGathering
{
    public ?string $type_name = null;
    public ?ElementType $current_type = null;


    /**
     * @var ElementType[] $parents
     */
    public array $parents = [];

    public bool $is_retired = false;
    public bool $is_final = false;

    /**
     * @param Request $request
     * @param ElementType|null $element_type
     * @throws ValidationException
     */
    public function __construct(Request $request,?ElementType $element_type = null)
    {
        try
        {
            DB::beginTransaction();
            if ($element_type) {
                $this->current_type = $element_type;
            } else {
                $this->current_type = $request->route('element_type');
            }


            //see if any parents, fail if a parent does not allow this

            if ( $request->request->has('parents')) {

                $parent_collection = $request->collect('parents');

                foreach ($parent_collection as $some_parent_hint) {

                    if (!is_string($some_parent_hint)) {
                        throw new HexbatchNotPossibleException(__('msg.child_types_must_be_string_names'),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ELEMENT_TYPE_BAD_SCHEMA);
                    }
                    /**
                     * @var ElementType|null $some_parent
                     */
                    $some_parent =  (new ElementType())->resolveRouteBinding($some_parent_hint);
                    $user_namespace = Utilities::getCurrentNamespace();
                    if ($some_parent->is_retired || $some_parent->is_final || !$some_parent->canNamespaceInherit($user_namespace)) {
                        throw new HexbatchNotPossibleException(__('msg.child_type_is_not_inheritable'),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ELEMENT_TYPE_CANNOT_INHERIT);
                    }
                    $this->parents[] = $some_parent;
                }

            }

            try {
                if ($this->type_name = $request->request->getString('type_name')) {
                    Validator::make(['type_name'=>$this->type_name], [
                        'type_name'=>['required','string',new ElementTypeNameReq($this->current_type)],
                    ])->validate();
                }
            } catch (ValidationException $v) {
                throw new HexbatchNotPossibleException($v->getMessage(),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ELEMENT_TYPE_INVALID_NAME);
            }

            if (!$this->type_name && !$this->current_type) {
                throw new HexbatchNotPossibleException(__('msg.element_type_must_have_name'),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ELEMENT_TYPE_INVALID_NAME);
            }


            $this->is_retired = Utilities::boolishToBool($request->get('is_retired',false));
            $this->is_final = Utilities::boolishToBool($request->get('is_final',false));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * @throws \Exception
     */
    public function assign() : ElementType {

        if(empty($this->current_type)) {
            $this->current_type = new ElementType();
            $current_namespace = Utilities::getCurrentNamespace();
            $this->current_type->owner_namespace_id = $current_namespace->id;
        }

        if ($this->type_name && !$this->current_type->isInUse()) {
            $this->current_type->type_name = $this->type_name;
        }



        $this->current_type->is_retired = $this->is_retired;
        $this->current_type->is_final = $this->is_final;

        try {
            DB::beginTransaction();
            $this->current_type->save();

            //fill in parents and hords
            foreach ($this->parents as $some_parent) {
                ElementTypeParent::addParent($some_parent,$this->current_type);
            }

            ElementTypeHorde::checkAttributeConflicts($this->current_type); //will throw when finds fist issue, and then will not save changes
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->current_type;
    }



    public static function doPing(Request $request,ElementType $type,AttributePingType $attribute_ping_type) : array
    {
        $ret = [];
        foreach ($type->type_attributes as $attribute) {
            $ret[$attribute->getName()] = AttributeGathering::doPing($request,$attribute,$attribute_ping_type);
        }



        $user_lookup = $request->get('user');

        /** @var User $check_user */
        $check_user = null;
        if ($user_lookup) {
            $check_user = (new User)->resolveRouteBinding($user_lookup);
        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_USER ||
            $attribute_ping_type === AttributePingType::READ || $attribute_ping_type === AttributePingType::READ_USER) {
            if (!$check_user) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }

            if($type->read_whitelist_group) {
                $ret['read'] = (bool)$type->read_whitelist_group->isMember($check_user->id);
            } else {
                $ret['read'] = true;
            }

        }

        if ($attribute_ping_type === AttributePingType::ALL || $attribute_ping_type === AttributePingType::ALL_USER
            || $attribute_ping_type === AttributePingType::WRITE || $attribute_ping_type === AttributePingType::WRITE_USER) {
            if (!$check_user) {
                throw new HexbatchNotPossibleException(__("msg.attribute_ping_missing_data"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_PING_DATA_MISSING);
            }

            if($type->write_whitelist_group) {
                $ret['write'] = (bool)$type->write_whitelist_group->isMember($check_user->id);
            } else {
                $ret['write'] = true;
            }

        }

        foreach ($type->type_parents as $parent) {
            $ret[$parent->getName()] = TypeGathering::doPing($request,$type,$attribute_ping_type);
        }

        return $ret;
    }



}
