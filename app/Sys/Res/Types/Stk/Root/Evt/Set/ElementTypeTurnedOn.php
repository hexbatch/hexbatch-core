<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementTypeTurnedOn extends Evt\ScopeSet
{
    const UUID = '39b1fd13-7625-4ce7-8945-05f88684fc76';
    const EVENT_NAME = TypeOfEvent::ELEMENT_TYPE_TURNED_ON;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

