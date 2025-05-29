<?php

namespace App\OpenApi\Callbacks;

use Hexbatch\Things\Models\ThingCallback;
use Illuminate\Contracts\Pagination\CursorPaginator;
use JsonSerializable;
use OpenApi\Attributes as OA;

/**
 * A collection of hooks
 */
#[OA\Schema(schema: 'HexbatchCallbackCollectionResponse',title: "Hooks")]
class HexbatchCallbackCollectionResponse implements  JsonSerializable
{




    #[OA\Property( title: 'List of callbacks')]
    /**
     * @var HexbatchCallbackResponse[] $hooks
     */
    public array $callbacks = [];

    /**
     * @param ThingCallback[]|\Illuminate\Database\Eloquent\Collection|CursorPaginator $given_callbacks
     */
    public function __construct($given_callbacks)
    {
        $this->callbacks = [];
        foreach ($given_callbacks as $callback) {
            $this->callbacks[] = new HexbatchCallbackResponse(callback: $callback);
        }

    }

    public function jsonSerialize(): array
    {
        $arr = [];

        $arr['callbacks'] = $this->callbacks;
        return $arr;
    }


}
