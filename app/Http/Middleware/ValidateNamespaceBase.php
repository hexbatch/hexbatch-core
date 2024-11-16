<?php

namespace App\Http\Middleware;

use App\Models\UserNamespace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class ValidateNamespaceBase
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
            throw new \LogicException("ValidateNamespaceBase does not see a Namespace in the parameter");
        }
        $this->checkPermission($namespace);
        return $next($request);
    }

    protected abstract function checkPermission(UserNamespace $user_namespace) ;

}
