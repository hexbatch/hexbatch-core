<?php

namespace App\Http\Middleware;

use App\Models\Attribute;
use App\Models\AttributeRule;
use App\Models\UserNamespace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * makes sure the rule belongs to the attribute
 */
class ValidateRuleOwnership
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
            throw new \LogicException("ValidateRuleOwnership does not see an attribute in the parameter");
        }

        /**
         * @var AttributeRule $rule
         */
        $rule = $request->route('attribute_rule');
        if (!$rule ) {
            throw new \LogicException("There is no element_type found in the route when asking for it");
        }
        if (!$rule instanceof AttributeRule) {
            throw new \LogicException("ValidateRuleOwnership does not see a rule in the parameter");
        }
        $this->checkPermission($attribute,$rule);
        return $next($request);
    }

    protected function checkPermission(Attribute $attribute,AttributeRule $rule) {
        $rule->checkRuleOwnership($attribute);
    }
}
