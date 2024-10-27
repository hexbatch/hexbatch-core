<?php

namespace App\Api\Actions\AInterfaces;

interface IActionLogic
{
    public static function doWork(IParamsJson|IParamsSystem|IParamsThing $params) : IDataOutput;
}
