<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PushCredentials extends Api\ElsewhereApi
{
    const UUID = '929d4904-2fcd-4130-b224-8d9c499f61dd';
    const TYPE_NAME = 'api_elsewhere_push_credentials';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewherePushCredentials::class,
    ];

}

