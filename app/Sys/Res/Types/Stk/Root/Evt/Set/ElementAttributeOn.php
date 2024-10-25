<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementAttributeOn extends Evt\ScopeSet
{
    const UUID = '987a2367-471d-4a85-9bb1-8d389825aa5d';
    const EVENT_NAME = TypeOfEvent::ELEMENT_ATTRIBUTE_ON;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

