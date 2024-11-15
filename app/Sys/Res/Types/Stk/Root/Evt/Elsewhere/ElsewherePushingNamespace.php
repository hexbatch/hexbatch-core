<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewherePushingNamespace extends Evt\ScopeElsewhere
{
    const UUID = '07ab8a09-bd17-4eb1-b719-ac25d4de3693';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_PUSHING_NAMESPACE;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

