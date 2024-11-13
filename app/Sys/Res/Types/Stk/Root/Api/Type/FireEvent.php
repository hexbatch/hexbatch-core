<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class FireEvent extends Api\TypeApi
{
    const UUID = 'f9d46900-c62e-489c-b487-cbea34b27a5a';
    const TYPE_NAME = 'api_type_fire_event';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ty\FireCustomEvent::class
    ];

}

