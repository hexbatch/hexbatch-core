<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Operation;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Push extends Api\OperationApi
{
    const UUID = '9206266c-5126-4eb1-b2ae-b098764805d9';
    const TYPE_NAME = 'api_operation_push';





    const PARENT_CLASSES = [
        Api\OperationApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Op\Push::class,
    ];

}

