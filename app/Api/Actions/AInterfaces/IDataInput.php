<?php

namespace App\Api\Actions\AInterfaces;

interface IDataInput
{
    public static function createFromParamsJson(IParamsJson $params) : IDataInput;
    public static function createFromParamsThing(IParamsThing $params) : IDataInput;
    public static function createFromParamsSystem(IParamsSystem $params) : IDataInput;
}
