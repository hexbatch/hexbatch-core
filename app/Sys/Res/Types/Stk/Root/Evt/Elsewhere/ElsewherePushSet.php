<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewherePushSet extends Evt\ScopeElsewhere
{
    const UUID = '81ec04ef-1d1a-43a2-b78a-dbf825d0f7ba';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_PUSH_SET;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

