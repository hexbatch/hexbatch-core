<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Operation;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Mutual extends Api\OperationApi
{
    const UUID = '732d7380-45fb-4f49-a17d-d1a085a24faf';
    const TYPE_NAME = 'api_operation_mutual';





    const PARENT_CLASSES = [
        Api\OperationApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Op\Mutual::class,
    ];

}

