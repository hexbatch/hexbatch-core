<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereSharingElement extends Evt\ScopeSet
{
    const UUID = '230a5ada-016e-4fd3-acd1-e50ca44c7e57';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_SHARING_ELEMENT;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

