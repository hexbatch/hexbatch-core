<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereGivesElement extends Evt\ScopeSet
{
    const UUID = 'b8ecd3d1-b4f3-4d62-aa92-5f11df7a2bfe';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_GIVES_ELEMENT;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

