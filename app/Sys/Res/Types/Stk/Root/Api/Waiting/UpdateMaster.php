<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class UpdateMaster extends Api\TypeApi
{
    const UUID = 'bb9ff39b-aad0-4fc5-8a61-653163717929';
    const TYPE_NAME = 'api_waiting_update_master';





    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Wa\SemaphoreMasterUpdate::class,
    ];

}

