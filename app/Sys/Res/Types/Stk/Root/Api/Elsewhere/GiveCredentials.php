<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class GiveCredentials extends Api\ElsewhereApi
{
    const UUID = '4f4d4bc7-c51c-464c-8005-215865837be4';
    const TYPE_NAME = 'api_elsewhere_give_credentials';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereGiveCredentials::class,
    ];

}

