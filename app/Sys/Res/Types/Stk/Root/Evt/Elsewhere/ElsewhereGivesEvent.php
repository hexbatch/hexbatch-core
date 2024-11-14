<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereGivesEvent extends Evt\ScopeElsewhere
{
    const UUID = '6f97cb56-0d85-4641-9360-b4f928cd585c';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_GIVES_EVENT;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

