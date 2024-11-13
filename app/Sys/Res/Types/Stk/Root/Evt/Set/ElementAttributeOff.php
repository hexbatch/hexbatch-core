<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementAttributeOff extends Evt\ScopeSet
{
    const UUID = '565365d3-c467-4b31-9a79-5e03166b959a';
    const EVENT_NAME = TypeOfEvent::ELEMENT_ATTRIBUTE_OFF;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

