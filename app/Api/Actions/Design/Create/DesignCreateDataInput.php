<?php

namespace App\Api\Actions\Design\Create;

use App\Api\Actions\AInterfaces\IDataInput;
use App\Api\Actions\AInterfaces\IParamsJson;
use App\Api\Actions\AInterfaces\IParamsSystem;
use App\Api\Actions\AInterfaces\IParamsThing;

class DesignCreateDataInput implements IDataInput
{

    public static function createFromParamsJson(IParamsJson $params): IDataInput
    {
        return new static;
    }

    public static function createFromParamsThing(IParamsThing $params): IDataInput
    {
        return new static;
    }

    public static function createFromParamsSystem(IParamsSystem $params): IDataInput
    {
        return new static;
    }
}
