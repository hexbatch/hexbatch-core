<?php

namespace App\Api\Actions\AInterfaces;

use App\Models\Thing;

interface IOutputThing
{
    public static function createFromData(Thing $thing ,IDataOutput $data) : IOutputThing;

}
