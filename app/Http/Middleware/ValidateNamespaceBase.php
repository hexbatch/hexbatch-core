<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchAuthException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\UserNamespace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as CodeOf;

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
            $namespace = Utilities::getThisUserDefaultNamespace();
            if (!$namespace) {
                throw new HexbatchAuthException(
                    __("msg.no_namespace"),
                    CodeOf::HTTP_UNAUTHORIZED,
                    RefCodes::NO_NAMESPACE);
            }

        }
        if (!$namespace instanceof UserNamespace) {
            throw new \LogicException("ValidateNamespaceBase does not see a Namespace in the parameter");
        }
        $this->checkPermission($namespace);
        return $next($request);
    }

    protected abstract function checkPermission(UserNamespace $user_namespace) ;

}
