<?php

namespace App\OpenApi\Callbacks;

use App\Api\Common\HexbatchUuid;
use App\Models\User;
use Carbon\Carbon;
use Hexbatch\Things\Interfaces\ICallResponse;
use Hexbatch\Things\Models\ThingCallback;
use JsonSerializable;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as CodeOf;

/**
 * Show callbacks
 */
#[OA\Schema(schema: 'HexbatchCallbackResponse')]
class HexbatchCallbackResponse implements  JsonSerializable
{

    public function __construct(protected ThingCallback $callback)
    {

    }


    public function jsonSerialize(): array
    {
       return $this->callback->toArray();
    }

}
