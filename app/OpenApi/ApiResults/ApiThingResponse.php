<?php

namespace App\OpenApi\ApiResults;


use App\OpenApi\ApiCallBase;

use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\OpenApi\Things\ThingMimimalResponseTrait;
use OpenApi\Attributes as OA;


/**
 * Show details about an attribute
 */
#[OA\Schema(schema: 'ApiThingResponse')]
class ApiThingResponse extends ApiCallBase
{
    use ThingMimimalResponseTrait;

    public function __construct(
        ?Thing $thing = null
    )
    {
        parent::__construct();
       $this->initThingFields(thing: $thing);
    }

}
