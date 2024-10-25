<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class SetChildDestroyed extends Evt\ScopeSet
{
    const UUID = '94a1f786-8df6-427b-94a7-65df725c5a39';
    const EVENT_NAME = TypeOfEvent::SET_CHILD_DESTROYED;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

