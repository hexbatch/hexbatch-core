<?php

namespace App\OpenApi\Params;

use App\OpenApi\ApiThingBase;
use Hexbatch\Things\Models\Thing;


class ApiParamBase extends  ApiThingBase
{
    public function __construct(?Thing $thing = null)
    {
        parent::__construct(thing:$thing);
    }
}
