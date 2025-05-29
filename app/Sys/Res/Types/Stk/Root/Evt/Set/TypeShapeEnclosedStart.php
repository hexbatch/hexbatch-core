<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeShapeEnclosedStart extends Evt\ScopeSet
{
    const UUID = '42fa5fec-df55-4e71-97b5-09f00e79337e';
    const EVENT_NAME = TypeOfEvent::TYPE_SHAPE_ENCLOSED_START;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

