<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeShapeEnclosingStart extends Evt\ScopeSet
{
    const UUID = '05f17f89-5d87-420a-af36-7a4b86ed613d';
    const EVENT_NAME = TypeOfEvent::TYPE_SHAPE_ENCLOSING_START;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

