<?php

namespace App\Api\Cmd;

use App\Models\Thing;

/**
 * used by the thing to write to the thing data
 */
interface IActionWorkReturn
{
    public function toThing(Thing $thing);

}
