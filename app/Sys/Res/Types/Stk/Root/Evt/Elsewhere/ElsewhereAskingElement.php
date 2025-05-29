<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereAskingElement extends Evt\ScopeElsewhere
{
    const UUID = '9b351b77-985e-4d95-9e99-6c75ae98d385';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_ASKING_ELEMENT;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

