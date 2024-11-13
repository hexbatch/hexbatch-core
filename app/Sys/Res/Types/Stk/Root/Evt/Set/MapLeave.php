<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class MapLeave extends Evt\ScopeSet
{
    const UUID = '2cefa211-f66b-4e47-9d52-6fceb1132d4e';
    const EVENT_NAME = TypeOfEvent::MAP_LEAVE;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

