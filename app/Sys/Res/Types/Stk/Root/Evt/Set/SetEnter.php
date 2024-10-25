<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class SetEnter extends Evt\ScopeSet
{
    const UUID = '946fb6f0-12bd-434b-8e34-e350bb38967a';
    const EVENT_NAME = TypeOfEvent::SET_ENTER;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

