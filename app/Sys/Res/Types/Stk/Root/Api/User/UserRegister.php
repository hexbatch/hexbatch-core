<?php

namespace App\Sys\Res\Types\Stk\Root\Api\User;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class UserRegister extends Api\UserApi
{
    const UUID = '6608f89f-ec12-427e-a653-9edc8acc5d19';
    const TYPE_NAME = 'api_user_register';





    const PARENT_CLASSES = [
        Api\UserApi::class,
        Act\Cmd\Us\UserRegister::class,
    ];

}

