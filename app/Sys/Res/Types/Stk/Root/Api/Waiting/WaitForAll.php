<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class WaitForAll extends Api\TypeApi
{
    const UUID = 'e46fe824-5fc3-4a07-974f-01fefb114bc1';
    const TYPE_NAME = 'api_waiting_wait_all';





    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Wa\WaitAll::class,
    ];

}

