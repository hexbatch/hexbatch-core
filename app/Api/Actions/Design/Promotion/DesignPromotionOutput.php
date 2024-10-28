<?php

namespace App\Api\Actions\Design\Promotion;

use App\Api\Actions\AInterfaces\IActionWorkReturn;
use App\Models\ThingResult;

/**
 * todo this is found by the thing in the action map to generate the output from the data of the results given to it by the @see IActionWorkReturn
 */
class DesignPromotionOutput implements IDesignPromotionActionOutput
{


    public function setThingOutput(ThingResult $result): array
    {
        //todo fill out the output
        return [];
    }
}
