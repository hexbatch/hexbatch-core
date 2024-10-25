<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementTypeOff extends Evt\ScopeSet
{
    const UUID = 'ca462f72-13f6-4acc-8670-6380cef18244';
    const EVENT_NAME = TypeOfEvent::ELEMENT_TYPE_OFF;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

