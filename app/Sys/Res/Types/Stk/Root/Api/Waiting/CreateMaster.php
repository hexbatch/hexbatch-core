<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class CreateMaster extends Api\TypeApi
{
    const UUID = '21aeb6ed-27ce-4110-8258-ea8300570a7c';
    const TYPE_NAME = 'api_waiting_create_master';





    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Wa\SemaphoreMasterCreate::class,
    ];

}

