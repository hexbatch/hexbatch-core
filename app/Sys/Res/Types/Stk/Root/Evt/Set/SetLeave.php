<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class SetLeave extends Evt\ScopeSet
{
    const UUID = '21104b44-14fc-44e3-a632-80113d48988d';
    const EVENT_NAME = TypeOfEvent::SET_LEAVE;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

