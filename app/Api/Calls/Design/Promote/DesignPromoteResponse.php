<?php
namespace App\Api\Calls\Design\Promote;


use App\Api\Calls\IApiThingResult;
use App\Api\IApiOaResponse;
use App\Models\Thing;
use App\Models\ThingResult;
use App\Sys\Res\Types\Stk\Root\Api\Design\Promotion;

/**
 * This is only called by the thing if there is no errors
 */
class DesignPromoteResponse  extends Promotion implements IApiThingResult,IApiOaResponse
{

    public function writeReturn(ThingResult $result): void
    {

    }

    public function processChildrenData(Thing $thing): void
    {
        // TODO: Implement processChildrenData() method.
    }
}
