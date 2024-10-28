<?php

namespace App\Api\Actions\Design\Create;

use App\Api\Actions\AInterfaces\IDataOutput;
use App\Api\Actions\AInterfaces\IOutputJson;
use App\Api\Actions\AInterfaces\IOutputThing;
use App\Models\ElementType;

class DataOutput implements IDataOutput
{


    public function __construct(protected ElementType $type)
    {

    }


    public function getOutputJson(): ?IOutputJson
    {
        return null;
    }

    public function getOutputThing(): ?IOutputThing
    {
        return null;
    }
}
