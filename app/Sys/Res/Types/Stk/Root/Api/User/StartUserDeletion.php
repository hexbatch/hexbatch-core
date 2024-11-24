<?php

namespace App\Sys\Res\Types\Stk\Root\Api\User;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class StartUserDeletion extends Api\UserApi
{
    const UUID = '2bf19367-618b-4ef4-8b56-00b2e6717f7d';
    const TYPE_NAME = 'api_user_start_deletion';





    const PARENT_CLASSES = [
        Api\UserApi::class,
        Act\Cmd\Us\UserStartDeletion::class,
    ];

}

