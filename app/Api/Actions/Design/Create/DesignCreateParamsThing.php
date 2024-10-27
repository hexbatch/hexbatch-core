<?php

namespace App\Api\Actions\Design\Create;

use App\Api\Actions\AInterfaces\IDataInput;
use App\Api\Actions\AInterfaces\IParamsThing;
use App\Models\Thing;

class DesignCreateParamsThing implements IParamsThing
{

    public function getInputData(): IDataInput
    {
        // TODO: Implement getInputData() method.
    }

    public static function createFromThing(Thing $thing): IParamsThing
    {
        // TODO: Implement createFromThing() method.
    }

    public function getThing(): Thing
    {
        // TODO: Implement getThing() method.
    }
}
