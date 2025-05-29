<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ReadingTime extends Evt\ScopeElement
{
    const UUID = '25738ee0-688c-45bc-ad02-96ed1ee9c6f6';
    const EVENT_NAME = TypeOfEvent::TIME_READING;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

