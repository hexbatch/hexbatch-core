<?php

namespace App\Http\Middleware;

use App\Models\Attribute;
use App\Models\ElementType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateAttributeOwnership
{
    /**
     * See if the owner of the namespace
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var Attribute $attribute
         */
        $attribute = $request->route('attribute');
        if (!$attribute ) {
            throw new \LogicException("There is no attribute found in the route when asking for it");
        }
        if (!$attribute instanceof Attribute) {
            throw new \LogicException("ValidateAttributeOwnership does not see an attribute in the parameter");
        }

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
        $this->checkPermission($attribute,$owner);
        return $next($request);
    }

    protected function checkPermission(Attribute $attribute,ElementType $owner) {
        $attribute->checkAttributeOwnership($owner);
    }
}
