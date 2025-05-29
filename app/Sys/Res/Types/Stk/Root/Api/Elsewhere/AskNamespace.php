<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AskNamespace extends Api\ElsewhereApi
{
    const UUID = '5992b0f3-d556-41b8-8661-f5654fa3b18d';
    const TYPE_NAME = 'api_elsewhere_ask_namespace';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereAskNamespace::class,
    ];

}

