<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ShowMutex extends BaseType
{
    const UUID = '9916fdd3-9837-4bc6-b352-2f3557fe852c';
    const TYPE_NAME = 'api_waiting_show_mutex';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\WaitingApi::class
    ];

}

