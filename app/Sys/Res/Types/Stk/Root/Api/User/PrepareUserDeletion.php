<?php

namespace App\Sys\Res\Types\Stk\Root\Api\User;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PrepareUserDeletion extends Api\UserApi
{
    const UUID = '910756fc-4964-452f-9360-cfed59bd6938';
    const TYPE_NAME = 'api_user_prepare_deletion';





    const PARENT_CLASSES = [
        Api\UserApi::class,
        Act\Cmd\Us\UserPrepareDeletion::class,
    ];

}

