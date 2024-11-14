<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ReadingVisual extends Evt\ScopeElement
{
    const UUID = '7b7736e8-8fec-4456-9279-a8d11e8bb633';
    const EVENT_NAME = TypeOfEvent::DISPLAY_READING;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

