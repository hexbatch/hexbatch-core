<?php

namespace App\OpenApi\Results\Callbacks;

use App\OpenApi\Results\ResultBase;
use Hexbatch\Things\Models\ThingCallback;
use OpenApi\Attributes as OA;

/**
 * Show callbacks
 */
#[OA\Schema(schema: 'HexbatchCallbackResponse')]
class HexbatchCallbackResponse extends ResultBase
{

    public function __construct(protected ThingCallback $callback)
    {
        parent::__construct(thing: $this->callback->thing_source);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();

        $other = $this->callback->toArray();
        return array_merge($ret,$other);
    }

}
