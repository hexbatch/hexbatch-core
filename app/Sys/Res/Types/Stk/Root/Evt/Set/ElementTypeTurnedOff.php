<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementTypeTurnedOff extends Evt\ScopeSet
{
    const UUID = '3672f617-ba23-4c71-b90b-5f56a78afd25';
    const EVENT_NAME = TypeOfEvent::ELEMENT_TYPE_TURNED_OFF;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

