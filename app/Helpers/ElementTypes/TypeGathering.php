<?php

namespace App\Helpers\ElementTypes;

use App\Enums\Attributes\AttributePingType;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Attributes\AttributeGathering;
use App\Helpers\UserGroups\GroupGathering;
use App\Helpers\Utilities;
use App\Models\ElementType;
use App\Models\UserGroup;
use App\Rules\ElementTypeNameReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TypeGathering
{
    public ?string $type_name = null;
    public ?ElementType $current_type = null;
    public ?UserGroup $editing_user_group = null;
    public ?UserGroup $inheriting_user_group = null;
    public ?UserGroup $new_elements_user_group = null;

    public bool $is_retired = false;
    public bool $is_final = false;

    /**
     * @param Request $request
     * @param ElementType|null $element_type
     *
     */
    public function __construct(Request $request,?ElementType $element_type = null)
    {
        if ($element_type) {
            $this->current_type = $element_type;
        } else {
            $this->current_type = $request->route('element_type');
        }

        $this->adminCheck();

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

        $this->editing_user_group = null;
        $this->inheriting_user_group = null;
        $this->new_elements_user_group = null;


        if ($editing_user_group = $request->get('editing_user_group')) {
            $this->editing_user_group = GroupGathering::adminCheckOrMakeGroupWithUserAdmin($editing_user_group);
        }

        if ($inheriting_user_group = $request->get('inheriting_user_group')) {
            $this->inheriting_user_group = GroupGathering::adminCheckOrMakeGroupWithUserAdmin($inheriting_user_group);
        }

        if ($new_elements_user_group = $request->get('new_elements_user_group')) {
            $this->new_elements_user_group = GroupGathering::adminCheckOrMakeGroupWithUserAdmin($new_elements_user_group);
        }

        $this->is_retired = Utilities::boolishToBool($request->get('is_retired',false));
        $this->is_final = Utilities::boolishToBool($request->get('is_final',false));

    }

    public function assign() : ElementType {
        if(empty($this->current_type)) {
            $this->current_type = new ElementType();
            $user = Utilities::getTypeCastedAuthUser();
            $this->current_type->user_id = $user->id;
        }

        if ($this->type_name && !$this->current_type->isInUse()) {
            $this->current_type->type_name = $this->type_name;
        }
        if ($this->editing_user_group) {
            $this->current_type->editing_user_group_id = $this->editing_user_group->id;
        }
        if ($this->inheriting_user_group) {
            $this->current_type->inheriting_user_group_id = $this->inheriting_user_group->id;
        }
        if ($this->new_elements_user_group) {
            $this->current_type->new_elements_user_group_id = $this->new_elements_user_group->id;
        }
        $this->current_type->is_retired = $this->is_retired;
        $this->current_type->is_final = $this->is_final;
        $this->current_type->save();

        return $this->current_type;
    }

    public function adminCheck() {
        if (!$this->current_type) {return;}

        $user = Utilities::getTypeCastedAuthUser();
        if ($this->current_type->canUserEdit($user)) {return;}

        throw new HexbatchPermissionException(__("msg.element_type_not_admin",['ref'=>$this->current_type->getName()]),
            \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
            RefCodes::ELEMENT_TYPE_NOT_AUTHORIZED);

    }


    public static function doPing(Request $request,ElementType $type,AttributePingType $attribute_ping_type) : array
    {
        $ret = [];
        foreach ($type->type_attributes as $attribute) {
            $newt = AttributeGathering::doPing($request,$attribute,$attribute_ping_type);
            static::mergePingData($ret,$newt);
        }
        return $ret;
    }

    public static function mergePingData(array &$ret, array $newt) : void {
        if ($newt['read_time']??false) { $ret['read_time'] = $newt['read_time'];}
        if ($newt['write_time']??false) { $ret['write_time'] = $newt['write_time'];}
        if ($newt['read_map']??false) { $ret['read_map'] = $newt['read_map'];}
        if ($newt['write_map']??false) { $ret['write_map'] = $newt['write_map'];}
        if ($newt['read_shape']??false) { $ret['read_shape'] = $newt['read_shape'];}
        if ($newt['write_shape']??false) { $ret['write_shape'] = $newt['write_shape'];}
        if ($newt['read_user']??false) { $ret['read_user'] = $newt['read_user'];}
        if ($newt['write_user']??false) { $ret['write_user'] = $newt['write_user'];}

    }





}
