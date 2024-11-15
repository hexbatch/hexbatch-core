<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class GiveElement extends Api\ElsewhereApi
{
    const UUID = '7e309d2a-616f-4545-bc73-a2523accf932';
    const TYPE_NAME = 'api_elsewhere_give_element';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereGiveElement::class,
    ];

}

