<?php

namespace App\Api\Cmd;

use Illuminate\Support\Collection;

/**
 * told which thing to pull data from, then has getters for the worker
 */
interface IActionParams
{
    public function setupThingData(mixed $thing): void ;

    public function processChildrenData(mixed $thing): void;

    public function setupDataWithThing(mixed $thing): void ;

    public function fromCollection(Collection $collection);

}
