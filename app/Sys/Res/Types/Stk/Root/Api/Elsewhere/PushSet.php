<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PushSet extends Api\ElsewhereApi
{
    const UUID = 'feca0012-3df9-4558-b5c5-d7d90d14cec3';
    const TYPE_NAME = 'api_elsewhere_push_set';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewherePushSet::class,
    ];

}

