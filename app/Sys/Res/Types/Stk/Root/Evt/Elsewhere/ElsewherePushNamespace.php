<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewherePushNamespace extends Evt\ScopeSet
{
    const UUID = '07ab8a09-bd17-4eb1-b719-ac25d4de3693';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_PUSH_NAMESPACE;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

