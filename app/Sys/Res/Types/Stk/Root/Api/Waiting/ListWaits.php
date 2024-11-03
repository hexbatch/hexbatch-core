<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListWaits extends Api\TypeApi
{
    const UUID = '1012360a-8075-4cf1-b561-856c8b7b8459';
    const TYPE_NAME = 'api_waiting_list_waits';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Ele\Search::class,
    ];

}

