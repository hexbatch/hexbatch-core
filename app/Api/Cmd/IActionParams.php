<?php

namespace App\Api\Cmd;

use App\Models\Thing;
use Illuminate\Support\Collection;

/**
 * told which thing to pull data from, then has getters for the worker
 */
interface IActionParams
{
    public function fromThing(Thing $thing): void ;

    public function fromCollection(Collection $collection);

}
