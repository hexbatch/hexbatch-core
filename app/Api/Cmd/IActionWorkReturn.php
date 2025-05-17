<?php

namespace App\Api\Cmd;


/**
 * used by the thing to write to the thing data
 */
interface IActionWorkReturn
{
    public function toThing( $thing);

}
