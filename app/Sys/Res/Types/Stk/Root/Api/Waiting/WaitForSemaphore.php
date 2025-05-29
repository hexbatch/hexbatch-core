<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class WaitForSemaphore extends Api\TypeApi
{
    const UUID = 'c92c0362-bed5-4b26-a244-0fa22b21b012';
    const TYPE_NAME = 'api_waiting_wait_semaphore';





    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Wa\WaitSemaphore::class,
    ];

}

