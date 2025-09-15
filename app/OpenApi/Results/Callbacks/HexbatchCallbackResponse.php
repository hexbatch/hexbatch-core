<?php

namespace App\OpenApi\Results\Callbacks;

use App\OpenApi\Results\ResultBase;
use Hexbatch\Things\Models\ThingCallback;
use Hexbatch\Things\OpenApi\Callbacks\CallbackResponse;
use OpenApi\Attributes as OA;

/**
 * Show callbacks
 */
#[OA\Schema(schema: 'HexbatchCallbackResponse')]
class HexbatchCallbackResponse extends ResultBase
{

    #[OA\Property( title: 'Callback')]

    public CallbackResponse $callback;
    public function __construct(ThingCallback $callback)
    {
        parent::__construct();
        $this->callback = new CallbackResponse(callback: $callback,b_include_hook: true,b_include_thing: true,b_alerted_by: true);
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['callback'] = $this->callback;
        return $ret;
    }

}
