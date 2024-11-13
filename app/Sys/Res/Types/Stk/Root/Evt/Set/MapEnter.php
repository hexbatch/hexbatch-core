<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class MapEnter extends Evt\ScopeSet
{
    const UUID = 'b2f7d7b0-c1b9-4e6b-abad-2176b7c2009a';
    const EVENT_NAME = TypeOfEvent::MAP_ENTER;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

