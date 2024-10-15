<?php

namespace App\Http\Middleware;


use App\Models\ElementType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * makes sure the rule belongs to the attribute
 */
class ValidateTypeNotInUse
{
    /**
     * See if the owner of the namespace
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        /**
         * @var ElementType $owner
         */
        $owner = $request->route('element_type');
        if (!$owner ) {
            throw new \LogicException("There is no element_type found in the route when asking for it");
        }
        if (!$owner instanceof ElementType) {
            throw new \LogicException("ValidateAttributeOwnership does not see an element_type in the parameter");
        }
        $this->checkPermission($owner);
        return $next($request);
    }

    protected function checkPermission(ElementType $type) {
        $type->checkInUse();
    }
}
