<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\UserNamespace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateNamespaceOwner
{
    /**
     * See if the owner of the namespace
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var UserNamespace $namespace
         */
        $namespace = $request->route('user_namespace');
        if (!$namespace ) {
            throw new \LogicException("There is no namespace found in the route when asking for it");
        }
        if (!$namespace instanceof UserNamespace) {
            throw new \LogicException("ValidateNamespaceOwner does not see a Namespace in the parameter");
        }
        $this->checkPermission($namespace);
        return $next($request);
    }

    protected function checkPermission(UserNamespace $user_namespace) {
        if ($user_namespace->namespace_user_id !== Utilities::getTypeCastedAuthUser()->id) {
            throw new HexbatchPermissionException(__("msg.namespace_not_owner",['ref'=>$user_namespace->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_OWNER);
        }
    }
}
