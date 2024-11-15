<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PushElement extends Api\ElsewhereApi
{
    const UUID = '73f87b1f-55d5-4bd1-b995-8c332342da45';
    const TYPE_NAME = 'api_elsewhere_push_element';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewherePushElement::class,
    ];

}

