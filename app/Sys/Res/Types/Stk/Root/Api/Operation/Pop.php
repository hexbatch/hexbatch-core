<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Operation;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Pop extends Api\OperationApi
{
    const UUID = '6a85fce9-8c85-4f03-9b5a-b7b71f2c8053';
    const TYPE_NAME = 'api_operation_pop';





    const PARENT_CLASSES = [
        Api\OperationApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Op\Pop::class,
    ];

}

