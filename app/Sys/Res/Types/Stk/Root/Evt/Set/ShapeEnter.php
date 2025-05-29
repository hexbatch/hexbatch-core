<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ShapeEnter extends Evt\ScopeSet
{
    const UUID = '83069f69-7b30-42da-abe6-307830b1e72e';
    const EVENT_NAME = TypeOfEvent::SHAPE_ENTER;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

