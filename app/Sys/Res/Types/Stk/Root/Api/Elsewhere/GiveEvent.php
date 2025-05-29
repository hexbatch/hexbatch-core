<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class GiveEvent extends Api\ElsewhereApi
{
    const UUID = '354b5674-023e-4a01-945a-bcb9530028f3';
    const TYPE_NAME = 'api_elsewhere_give_event';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereGiveEvent::class,
    ];

}

