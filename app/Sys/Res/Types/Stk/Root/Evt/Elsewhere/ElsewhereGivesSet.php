<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereGivesSet extends Evt\ScopeElsewhere
{
    const UUID = 'b8d80b61-582f-4158-be60-da2165ba6f17';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_GIVES_SET;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

