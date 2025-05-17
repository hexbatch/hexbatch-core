<?php

namespace App\Api\Calls;



/**
 * will fill in the top thing result with data and error conditions,
 * called and created by the thing, this writes json using output classes defined by open-api return stuff and json driven by that
 */
interface IApiThingResult
{
    public function processChildrenData(  $thing): void;
    public function writeReturn(  $result): void;
}
