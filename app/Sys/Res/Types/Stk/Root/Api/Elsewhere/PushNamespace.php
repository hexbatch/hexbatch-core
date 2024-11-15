<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PushNamespace extends Api\ElsewhereApi
{
    const UUID = '5b8d36e1-6198-4ee0-b65c-00f4165d1290';
    const TYPE_NAME = 'api_elsewhere_push_namespace';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewherePushNamespace::class,
    ];

}

