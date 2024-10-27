<?php

namespace App\Api\Actions\AInterfaces;

interface IOutputJson
{
    public static function createFromData(IDataOutput $data) : IOutputJson;

    public function toJsonArray() : array ;
}
