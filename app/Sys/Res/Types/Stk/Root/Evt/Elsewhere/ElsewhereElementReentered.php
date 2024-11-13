<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereElementReentered extends Evt\ScopeSet
{
    const UUID = 'ba2a7b72-65a0-4948-b201-dba33ef453c1';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_ELEMENT_REENTERED;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

