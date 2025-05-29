<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ShapeLeave extends Evt\ScopeSet
{
    const UUID = '405cf23f-28c3-4c39-885d-e8fde0220e6f';
    const EVENT_NAME = TypeOfEvent::SHAPE_LEAVE;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

