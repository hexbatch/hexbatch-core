<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AskCredentials extends Api\ElsewhereApi
{
    const UUID = '4ff5b5f2-86d4-4553-b4f3-8f3de2b1f3e3';
    const TYPE_NAME = 'api_elsewhere_ask_credentials';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereAskCredentials::class,
    ];

}

