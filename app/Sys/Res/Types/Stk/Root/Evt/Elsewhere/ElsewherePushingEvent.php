<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewherePushingEvent extends Evt\ScopeElsewhere
{
    const UUID = '6f11a8aa-abec-41a3-8901-9f58edf86805';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_PUSHING_EVENT;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

