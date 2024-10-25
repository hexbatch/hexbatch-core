<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewherePushEvent extends Evt\ScopeSet
{
    const UUID = '6f11a8aa-abec-41a3-8901-9f58edf86805';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_PUSH_EVENT;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

