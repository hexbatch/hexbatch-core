<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewherePushingSet extends Evt\ScopeElsewhere
{
    const UUID = '81ec04ef-1d1a-43a2-b78a-dbf825d0f7ba';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_PUSHING_SET;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

