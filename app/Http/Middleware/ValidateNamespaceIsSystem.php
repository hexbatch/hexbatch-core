<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Models\UserNamespace;
use Symfony\Component\HttpFoundation\Response;

class ValidateNamespaceIsSystem extends ValidateNamespaceOwner
{

    protected function checkPermission(UserNamespace $user_namespace) {
        if (!$user_namespace->is_system  ) {
            throw new HexbatchPermissionException(__("msg.namespace_not_system",['ref'=>$user_namespace->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_ADMIN);
        }
    }
}
