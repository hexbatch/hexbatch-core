<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereSuspendedType extends Evt\ScopeSet
{
    const UUID = 'dbb69c4c-444c-4c43-929e-a16ad2eabceb';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_SUSPENDED_TYPE;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

