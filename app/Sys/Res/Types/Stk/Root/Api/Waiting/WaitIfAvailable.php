<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class WaitIfAvailable extends Api\TypeApi
{
    const UUID = '41a80897-123b-4b72-88e0-2e7f4e4bb8cc';
    const TYPE_NAME = 'api_waiting_wait_if_available';





    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Wa\WaitAvailable::class,
    ];

}

