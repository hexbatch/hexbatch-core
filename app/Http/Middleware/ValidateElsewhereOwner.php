<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Models\Server;
use App\Models\UserNamespace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateElsewhereOwner
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

        /**
         * @var Server $elsewhere
         */
        $elsewhere = $request->route('elsewhere');
        if (!$elsewhere ) {
            throw new \LogicException("There is no elsewhere found in the route when asking for it");
        }
        if (!$elsewhere instanceof Server) {
            throw new \LogicException("ValidateNamespaceOwner does not see a Server in the parameter");
        }
        $this->checkPermission($namespace,$elsewhere);
        return $next($request);
    }

    protected  function checkPermission(UserNamespace $user_namespace,Server $elsewhere) {
        if ($elsewhere->is_system) {
            throw new HexbatchPermissionException(__("msg.this_server_is_not_elsewhere"),
                Response::HTTP_I_AM_A_TEAPOT,
                RefCodes::NAMESPACE_NOT_ADMIN);
        }
        if ( $user_namespace->id !== $elsewhere->owning_namespace_id ) {
            throw new HexbatchPermissionException(__("msg.not_elsewhere_owner",['ref'=>$user_namespace->getName(),'server'=>$elsewhere->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_ADMIN);
        }
    }

}
