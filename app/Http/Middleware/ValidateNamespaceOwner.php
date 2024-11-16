<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\UserNamespace;
use Symfony\Component\HttpFoundation\Response;

class ValidateNamespaceOwner extends ValidateNamespaceBase
{

    protected function checkPermission(UserNamespace $user_namespace) {
        if ($user_namespace->namespace_user_id !== Utilities::getTypeCastedAuthUser()->id) {
            throw new HexbatchPermissionException(__("msg.namespace_not_owner",['ref'=>$user_namespace->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_OWNER);
        }
    }
}
