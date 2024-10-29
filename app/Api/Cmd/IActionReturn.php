<?php

namespace App\Api\Cmd;

use App\Models\HexError;
use App\Models\Thing;

/**
 * used by the thing to write to the thing data
 */
interface IActionReturn
{
    public function writeData(Thing $thing);

    public function getHexError() : ?HexError;
}
