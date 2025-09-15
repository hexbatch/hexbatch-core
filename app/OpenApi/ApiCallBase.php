<?php

namespace App\OpenApi;


use Hexbatch\Things\Interfaces\IThingBaseResponse;

abstract class ApiCallBase extends ApiCollectionBase implements IThingBaseResponse
{
    use ApiDataTrait;

    public function __construct()
    {

    }
}
