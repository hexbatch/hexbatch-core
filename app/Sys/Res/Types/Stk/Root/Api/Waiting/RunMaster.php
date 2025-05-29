<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Waiting;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RunMaster extends Api\TypeApi
{
    const UUID = '50598ae9-91f8-4fb3-9d54-d69fda5e707c';
    const TYPE_NAME = 'api_waiting_run_master';





    const PARENT_CLASSES = [
        Api\WaitingApi::class,
        Act\Cmd\Wa\SemaphoreMasterRun::class,
    ];

}

