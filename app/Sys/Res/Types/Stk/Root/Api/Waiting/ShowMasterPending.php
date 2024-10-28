<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Api;


class ShowMasterPending extends BaseType
{
    const UUID = '8bc2d280-884b-436e-baae-f3c15cf37d3b';
    const TYPE_NAME = 'api_waiting_show_master_pending';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\WaitingApi::class
    ];

}

