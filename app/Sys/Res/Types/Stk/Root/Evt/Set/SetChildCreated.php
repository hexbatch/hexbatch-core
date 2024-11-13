<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class SetChildCreated extends Evt\ScopeSet
{
    const UUID = '5db9e2bd-3175-45e8-87bc-67b05969d727';
    const EVENT_NAME = TypeOfEvent::SET_CHILD_CREATED;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

