<?php

namespace App\Api\Actions\Design\Create;

use App\Api\Actions\AInterfaces\IDataOutput;
use App\Api\Actions\AInterfaces\IOutputThing;

class DesignCreateOutputThing implements IOutputThing
{

    public static function createFromData(\App\Models\Thing $thing, IDataOutput $data): IOutputThing
    {
        // TODO: Implement createFromData() method.
    }

}
