<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class WaitForAny extends Api\TypeApi
{
    const UUID = '7354bf51-ba98-426a-ae48-57795f83861a';
    const TYPE_NAME = 'api_waiting_wait_any';





    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Wa\WaitAny::class,
    ];

}

