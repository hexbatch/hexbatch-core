<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereAskingNamespace extends Evt\ScopeElsewhere
{
    const UUID = '7928d4f1-485f-4125-9671-4f5b46d4f34a';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_ASKING_NAMESPACE;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

