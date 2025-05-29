<?php

namespace App\OpenApi\Results\Callbacks;

use App\OpenApi\Results\ResultCursorBase;
use Hexbatch\Things\Models\ThingCallback;
use Hexbatch\Things\OpenApi\Callbacks\CallbackResponse;
use Illuminate\Contracts\Pagination\CursorPaginator;

use OpenApi\Attributes as OA;

/**
 * A collection of hooks
 */
#[OA\Schema(schema: 'HexbatchCallbackCollectionResponse',title: "Callbacks")]
class HexbatchCallbackCollectionResponse extends ResultCursorBase
{


    #[OA\Property( title: 'List of callbacks')]
    /**
     * @var HexbatchCallbackResponse[] $callbacks
     */
    public array $callbacks = [];

    /**
     * @param ThingCallback[]|\Illuminate\Database\Eloquent\Collection|CursorPaginator $given_callbacks
     */
    public function __construct($given_callbacks,
                                 bool $b_include_hook = false,
                                 bool $b_include_thing = false,
                                 bool $b_alerted_by = false)
    {
        parent::__construct($given_callbacks);
        $this->callbacks = [];
        foreach ($given_callbacks as $callback) {
            $this->callbacks[] = new HexbatchCallbackResponse(callback: $callback,b_include_hook: $b_include_hook,
                b_include_thing: $b_include_thing,b_alerted_by: $b_alerted_by);
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();

        $ret['callbacks'] = $this->callbacks;
        return $ret;
    }


}
