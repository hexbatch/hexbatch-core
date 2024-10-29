<?php
namespace App\Api\Ta\Design\Promote;


use App\Api\Ta\IApiOutput;
use App\Models\ThingResult;

/**
 * This is only called by the thing if there is no errors
 */
class DesignPromoteApiOutput implements IApiOutput
{

    public function writeReturn(ThingResult $result): void
    {
        // TODO: Implement writeReturn() method.
    }

}
