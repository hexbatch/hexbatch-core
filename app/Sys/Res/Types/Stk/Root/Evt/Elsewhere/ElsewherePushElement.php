<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewherePushElement extends Evt\ScopeSet
{
    const UUID = '82864b92-1507-4597-805f-c1f8961e8de7';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_PUSH_ELEMENT;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

