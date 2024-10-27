<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class UpdateMaster extends BaseType
{
    const UUID = 'bb9ff39b-aad0-4fc5-8a61-653163717929';
    const TYPE_NAME = 'api_waiting_update_master';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\SemaphoreMasterUpdate::class,
    ];

}

