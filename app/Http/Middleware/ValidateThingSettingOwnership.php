<?php

namespace App\Http\Middleware;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Models\Thing;
use App\Models\ThingSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * makes sure the rule belongs to the attribute
 */
class ValidateThingSettingOwnership
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
            throw new \LogicException("ValidateThingSettingOwnership does not see a thing in the parameter");
        }

        /**
         * @var ThingSetting $setting
         */
        $setting = $request->route('thing_setting');
        if (!$setting ) {
            throw new \LogicException("ValidateThingSettingOwnership does not see any thing setting");
        }
        if (!$setting instanceof ThingSetting) {
            throw new \LogicException("ValidateThingSettingOwnership does not see a setting object");
        }
        $this->checkPermission($thing,$setting);
        return $next($request);
    }

    protected function checkPermission(Thing $thing,ThingSetting $setting) {
        $legit = $setting->checkSettingOwnership($thing);
        if (!$legit) {
            throw new HexbatchNotFound(
                __('msg.thing_owner_does_not_match_setting_given',['ref'=>$thing->getName(),'setting'=>$setting->getName()]),
                Response::HTTP_NOT_FOUND,
                RefCodes::THING_SETTING_NOT_FOUND
            );
        }
    }
}
