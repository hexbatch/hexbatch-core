<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Reset extends BaseType
{
    const UUID = '3b8bb2ce-480b-4a65-b054-078e5d720615';
    const TYPE_NAME = 'api_waiting_reset';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\SemaphoreReset::class,
    ];

}

