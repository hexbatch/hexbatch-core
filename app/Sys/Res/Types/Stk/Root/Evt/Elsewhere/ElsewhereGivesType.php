<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereGivesType extends Evt\ScopeSet
{
    const UUID = '74713eed-1042-4658-b71a-1f85f2c0bd44';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_GIVES_TYPE;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

