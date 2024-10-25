<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;


use App\Enums\Sys\TypeOfEvent;

class ScopeElement extends BaseEvent
{
    const UUID = 'cc593a6d-76ce-4835-9f43-2a1f14699647';
    const EVENT_NAME = TypeOfEvent::EVENT_SCOPE_ELEMENT;



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        BaseEvent::class
    ];

}

