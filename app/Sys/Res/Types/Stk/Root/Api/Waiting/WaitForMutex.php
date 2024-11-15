<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class WaitForMutex extends Api\TypeApi
{
    const UUID = '6b22c2ed-02d4-4d24-870c-82369fec029b';
    const TYPE_NAME = 'api_waiting_wait_mutex';





    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Wa\WaitMutex::class,
    ];

}

