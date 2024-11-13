<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ShapeDisplayWrite extends Evt\ScopeElement
{
    const UUID = '336262b6-7c2d-46c4-82c7-3a9ef1720e31';
    const EVENT_NAME = TypeOfEvent::SHAPE_DISPLAY_WRITE;







    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

