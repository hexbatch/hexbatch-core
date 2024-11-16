<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Models\Thing;
use App\Models\ThingHook;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * makes sure the rule belongs to the attribute
 */
class ValidateThingHookOwnership
{
    /**
     * See if the owner of the namespace
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var Thing $thing
         */
        $thing = $request->route('thing');
        if (!$thing ) {
            throw new \LogicException("There is no thing found in the route when asking for it");
        }
        if (!$thing instanceof Thing) {
            throw new \LogicException("ValidateThingHookOwnership does not see a thing in the parameter");
        }

        /**
         * @var ThingHook $hook
         */
        $hook = $request->route('thing_hook');
        if (!$hook ) {
            throw new \LogicException("ValidateThingHookOwnership does not see any thing hook");
        }
        if (!$hook instanceof ThingHook) {
            throw new \LogicException("ValidateThingHookOwnership does not see a hook object");
        }
        $this->checkPermission($thing,$hook);
        return $next($request);
    }

    protected function checkPermission(Thing $thing,ThingHook $hook) {
        $legit = $hook->checkHookOwnership($thing);
        if (!$legit) {
            throw new HexbatchNotFound(
                __('msg.thing_owner_does_not_match_hook_given',['ref'=>$thing->getName(),'hook'=>$hook->getName()]),
                Response::HTTP_NOT_FOUND,
                RefCodes::THING_HOOK_NOT_FOUND
            );
        }
    }
}
