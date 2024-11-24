<?php

namespace App\Sys\Res\Types\Stk\Root\Api\User;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Login extends Api\UserApi
{
    const UUID = '15fbdea6-46b4-4c2a-8adc-46cda172e288';
    const TYPE_NAME = 'api_user_login';





    const PARENT_CLASSES = [
        Api\UserApi::class,
        Act\Cmd\Us\UserLogin::class,
    ];

}

