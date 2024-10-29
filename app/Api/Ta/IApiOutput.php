<?php

namespace App\Api\Ta;

use App\Models\ThingResult;

/**
 * will fill in the top thing result with data and error conditions,
 * called and created by the thing, this writes json using output classes defined by open-api return stuff and json driven by that
 */
interface IApiOutput
{
    public function writeReturn( ThingResult $result): void;
}
