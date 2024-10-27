<?php

namespace App\Api\Actions\AInterfaces;

use App\Models\Thing;

interface IParamsThing
{
    public static function createFromThing(Thing $thing) : IParamsThing;

    public function getInputData() : IDataInput;
    public function getThing() : Thing;
}
