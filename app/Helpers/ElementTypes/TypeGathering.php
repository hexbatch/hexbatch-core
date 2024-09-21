<?php

namespace App\Helpers\ElementTypes;

use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
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
     * @throws ValidationException
     */
    public function __construct(Request $request,?ElementType $element_type = null)
    {
        if ($element_type) {
            $this->current_type = $element_type;
        } else {
            $this->current_type = $request->route('element_type');
        }

        $this->adminCheck();

        if ($this->type_name = $request->request->getString('type_name',null)) {
            Validator::make(['type_name'=>$this->type_name], [
                'type_name'=>['required','string',new ElementTypeNameReq($this->current_type)],
            ])->validate();
        }
        $this->editing_user_group = null;
        $this->inheriting_user_group = null;
        $this->new_elements_user_group = null;


        if ($editing_user_group = $request->get('editing_user_group')) {
            $this->editing_user_group = $this->checkGroup($editing_user_group);
        }

        if ($inheriting_user_group = $request->get('inheriting_user_group')) {
            $this->inheriting_user_group = $this->checkGroup($inheriting_user_group);
        }

        if ($new_elements_user_group = $request->get('new_elements_user_group')) {
            $this->new_elements_user_group = $this->checkGroup($new_elements_user_group);
        }

        $this->is_retired = Utilities::boolishToBool($request->get('is_retired',false));
        $this->is_final = Utilities::boolishToBool($request->get('is_final',false));

    }

    public function assign() : ElementType {
        if(empty($this->current_type)) {
            $this->current_type = new ElementType();
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
        if ($this->current_type->type_owner?->inAdminGroup($user->id) ) { return; }
        if ($this->current_type->editing_group?->isMember($user->id) ) { return; }

        throw new HexbatchPermissionException(__("msg.element_type_not_admin",['ref'=>$this->current_type->getName()]),
            \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
            RefCodes::ELEMENT_TYPE_NOT_AUTHORIZED);

    }

    /**
     * @throws ValidationException
     */
    public function checkGroup($group_ref_or_id_or_array) : ?UserGroup{
        if (empty($group_ref_or_id_or_array)) {return null;}

        $user = Utilities::getTypeCastedAuthUser();

        $user_group = null;
        if (is_string($group_ref_or_id_or_array)) {
            /**
             * @var UserGroup $user_group
             */
            $user_group = (new UserGroup())->resolveRouteBinding($group_ref_or_id_or_array);
            if(!$user_group->isAdmin($user->id)) {
                throw new HexbatchPermissionException(__('msg.group_not_admin',['username'=>$user->getName(),'group'=>$user_group->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                    RefCodes::ELEMENT_TYPE_INVALID_GROUP);
            }
        } else {
            if (is_array($group_ref_or_id_or_array) && isset($group_ref_or_id_or_array['group_name'])) {
                $user_group = GroupGathering::SetupNewGroup($group_ref_or_id_or_array['group_name']);
            }
        }
        return $user_group;
    }


}
