<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListMutexes extends Api\TypeApi
{
    const UUID = '582946c4-b68a-4442-b756-ba15b3f0c991';
    const TYPE_NAME = 'api_waiting_list_mutexes';





    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Pa\Search::class,
    ];

}

