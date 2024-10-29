<?php

namespace App\Api\Cmd;

use App\Models\Thing;

/**
 * told which thing to pull data from, then has getters for the worker
 */
interface IActionParams
{
    public function pullData(Thing $thing): void ;
}
