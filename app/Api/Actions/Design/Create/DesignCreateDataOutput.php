<?php

namespace App\Api\Actions\Design\Create;

use App\Api\Actions\AInterfaces\IDataOutput;
use App\Api\Actions\AInterfaces\IOutputJson;
use App\Api\Actions\AInterfaces\IOutputThing;

class DesignCreateDataOutput implements IDataOutput
{

    public function getOutputJson(): ?IOutputJson
    {
        return null;
    }

    public function getOutputThing(): ?IOutputThing
    {
        return null;
    }
}
