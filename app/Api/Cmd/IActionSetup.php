<?php

namespace App\Api\Cmd;

use App\Models\Thing;

/**
 * told from which thing to collect data, will write data (json ,ids, etc)
 */
interface IActionSetup
{
    public function pushData(Thing $thing): void ;
}
