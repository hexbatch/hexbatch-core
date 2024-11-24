<?php
namespace App\Api\Calls\Design\Promote;


use App\Api\Calls\IApiThingResult;
use App\Api\IApiOaResponse;
use App\Models\ThingResult;
use App\Sys\Res\Types\Stk\Root\Api\Design\Promotion;

/**
 * This is only called by the thing if there is no errors
 */
class DesignPromoteResponse  extends Promotion implements IApiThingResult,IApiOaResponse
{

    /**
     * @param DesignPromoteResponse $api_result
     */
    public static function writeReturn(ThingResult $result, $api_result): void
    {

    }

}
