<?php

namespace App\Api\Actions\AInterfaces;
use App\Models\Thing;


interface IActionLogic
{

    public static function doWork(Thing $thing, $params): IActionWorkReturn;

}
