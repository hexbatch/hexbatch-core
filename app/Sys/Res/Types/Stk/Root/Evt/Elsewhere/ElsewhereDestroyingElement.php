<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereDestroyingElement extends Evt\ScopeElsewhere
{
    const UUID = '90134989-7edd-4435-9731-d423c7e9388a';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_DESTROYED_ELEMENT;



    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

