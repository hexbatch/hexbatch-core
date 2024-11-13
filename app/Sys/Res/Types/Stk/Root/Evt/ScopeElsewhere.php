<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;


use App\Enums\Sys\TypeOfEvent;

class ScopeElsewhere extends BaseEvent
{
    const UUID = 'f57fa3b6-a1be-4af8-b4a6-2590cd854e4e';
    const EVENT_NAME = TypeOfEvent::EVENT_SCOPE_ELSEWHERE;




    const PARENT_CLASSES = [
        BaseEvent::class
    ];

}

