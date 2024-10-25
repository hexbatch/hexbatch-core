<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;


use App\Enums\Sys\TypeOfEvent;

class ScopeType extends BaseEvent
{
    const UUID = '6d9ffce0-4922-4152-84c7-a9a5865b10bc';
    const EVENT_NAME = TypeOfEvent::EVENT_SCOPE_TYPE;




    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        BaseEvent::class
    ];

}

