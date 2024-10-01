<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\UserNamespace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateNamespaceMember extends ValidateNamespaceOwner
{

    protected function checkPermission(UserNamespace $user_namespace) {
        if (!$user_namespace->isUserMember(Utilities::getTypeCastedAuthUser())  ) {
            throw new HexbatchPermissionException(__("msg.namespace_not_member",['ref'=>$user_namespace->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_ADMIN);
        }
    }
}
