<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\UserNamespace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateTypeMembership
{
    /**
     * See if the owner of the namespace
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        /**
         * @var ElementType $type
         */
        $type = $request->route('element_type');
        if (!$type ) {
            throw new \LogicException("There is no element_type found in the route when asking for it");
        }
        if (!$type instanceof ElementType) {
            throw new \LogicException("ValidateAttributeOwnership does not see an element_type in the parameter");
        }

        /** @var UserNamespace $user_namespace */
        $user_namespace = $request->route('user_namespace');
        if (!$user_namespace->isNamespaceMember(namespace: null,ns_id: $user_namespace->id)  ) {
            throw new HexbatchPermissionException(__("msg.namespace_not_member",['ref'=>$user_namespace->getName()]),
                Response::HTTP_FORBIDDEN,
                RefCodes::NAMESPACE_NOT_MEMBER);
        }
        return $next($request);
    }

    protected function checkPermission(Attribute $attribute,ElementType $owner) {
        $attribute->checkAttributeOwnership($owner);
    }
}
