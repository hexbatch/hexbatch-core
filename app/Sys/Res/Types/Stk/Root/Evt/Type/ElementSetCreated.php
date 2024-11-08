<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * One or more elements were moved from a different phase
 */
class ElementSetCreated extends Evt\ScopeSet
{
    const UUID = 'fa06219b-a995-4bb2-85dd-5645a47fd67c';
    const EVENT_NAME = TypeOfEvent::ELEMENT_SET_CREATED;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

