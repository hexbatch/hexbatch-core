<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Operation;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Shift extends Api\OperationApi
{
    const UUID = '2c8d3bbc-f930-49c8-bf1d-039e5a5ef28f';
    const TYPE_NAME = 'api_operation_shift';





    const PARENT_CLASSES = [
        Api\OperationApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Op\Shift::class,
    ];

}

