<?php

namespace App\Api\Actions\Design\Create;

use App\Api\Actions\AInterfaces\IActionLogic;
use App\Api\Actions\AInterfaces\IDataOutput;
use App\Api\Actions\AInterfaces\IParamsJson;
use App\Api\Actions\AInterfaces\IParamsSystem;
use App\Api\Actions\AInterfaces\IParamsThing;

class DesignCreateLogic implements IActionLogic
{

    public static function doWork(IParamsSystem|IParamsJson|IParamsThing $params): IDataOutput
    {
        return new DesignCreateDataOutput();
    }
}
