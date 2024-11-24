<?php

namespace App\Api;


use Illuminate\Support\Collection;

/**
 * Marks a class which does the json input definition for the OA for success
 */
interface IApiOaParams
{
    public function fromCollection(Collection $collection);
}
