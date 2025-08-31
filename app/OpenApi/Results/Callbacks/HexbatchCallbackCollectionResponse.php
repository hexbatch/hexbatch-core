<?php

namespace App\OpenApi\Results\Callbacks;

use App\OpenApi\Results\ResultDataBase;
use Hexbatch\Things\Models\ThingCallback;
use Illuminate\Contracts\Pagination\CursorPaginator;

use OpenApi\Attributes as OA;

/**
 * A collection of hooks
 */
#[OA\Schema(schema: 'HexbatchCallbackCollectionResponse',title: "Callbacks")]
class HexbatchCallbackCollectionResponse extends ResultDataBase
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
        parent::__construct($given_callbacks);
        $this->callbacks = [];
        foreach ($given_callbacks as $callback) {
            $this->callbacks[] = new HexbatchCallbackResponse(callback: $callback);
        }

    }

    public  function toArray() : array  {
        $ret = parent::toArray();

        $ret['callbacks'] = $this->callbacks;
        return $ret;
    }


}
