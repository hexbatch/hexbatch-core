<?php


namespace App\Sys\Res\Types;

use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Models\UserNamespace;
use Symfony\Component\HttpFoundation\Response;

trait GroupTrait {
    protected static function checkIfGivenIsAdmin(UserNamespace $given , ?UserNamespace $target) {
        if (!$target) {
            throw new \LogicException("target namespace is null");
        }
        if (!$target->isNamespaceAdmin($given)  ) {
            throw new HexbatchPermissionException(__("msg.namespace_not_admin",['ref'=>$target->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_ADMIN);
        }
    }

    protected static function checkIfGivenIsOwner(UserNamespace $given , ?UserNamespace $target) {
        if (!$target) {
            throw new \LogicException("target namespace is null");
        }
        if (!$target->isNamespaceOwner($given)  ) {
            throw new HexbatchPermissionException(__("msg.namespace_not_owner",['ref'=>$target->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_ADMIN);
        }
    }

    protected static function checkIfGivenIsMember(UserNamespace $given , ?UserNamespace $target) {
        if (!$target) {
            throw new \LogicException("target namespace is null");
        }
        if (!$target->isNamespaceMember($given)  ) {
            throw new HexbatchPermissionException(__("msg.namespace_not_member",['ref'=>$target->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_ADMIN);
        }
    }
}
