<?php

namespace App\Sys\Res\Types\Stk\Root\Act;


use App\Enums\Sys\TypeOfAction;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Models\UserNamespace;
use Symfony\Component\HttpFoundation\Response;


class Cmd extends BaseAction
{
    const UUID = 'f4717906-b735-415d-80d0-6c17d4177595';

    const ACTION_NAME = TypeOfAction::BASE_COMMAND;


    const PARENT_CLASSES = [
        BaseAction::class
    ];

    public function getNamespaceInUse(): ?UserNamespace
    {
        return $this->action_data?->data_owner_namespace;
    }

    protected function checkIfAdmin(UserNamespace $target) {

        if (!$target->isNamespaceAdmin($this->getNamespaceInUse())  ) {
            throw new HexbatchPermissionException(__("msg.namespace_not_admin",['ref'=>$target->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_ADMIN);
        }
    }

    protected function checkIfMember(UserNamespace $target) {

        if (!$target->isNamespaceMember($this->getNamespaceInUse())  ) {
            throw new HexbatchPermissionException(__("msg.namespace_not_member",['ref'=>$target->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_ADMIN);
        }
    }


}

