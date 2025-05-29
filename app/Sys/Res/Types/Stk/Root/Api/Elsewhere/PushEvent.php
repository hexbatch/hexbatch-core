<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PushEvent extends Api\ElsewhereApi
{
    const UUID = '9bab9c9e-32e6-4d9b-b152-bb1ea6b5bd2a';
    const TYPE_NAME = 'api_elsewhere_push_event';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewherePushEvent::class,
    ];

}

