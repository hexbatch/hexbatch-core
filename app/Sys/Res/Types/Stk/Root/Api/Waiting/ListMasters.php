<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListMasters extends Api\TypeApi
{
    const UUID = 'ef318707-f38e-4395-90fe-144d5802d1c6';
    const TYPE_NAME = 'api_waiting_list_masters';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Ele\Search::class,
    ];

}

