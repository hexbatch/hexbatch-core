<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListSemaphores extends Api\TypeApi
{
    const UUID = '30b01334-c439-44ff-bba3-8be57e4da191';
    const TYPE_NAME = 'api_waiting_list_semaphores';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Ele\Search::class,
    ];

}

