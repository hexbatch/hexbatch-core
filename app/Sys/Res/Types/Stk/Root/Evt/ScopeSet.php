<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;


use App\Enums\Sys\TypeOfEvent;

class ScopeSet extends BaseEvent
{
    const UUID = 'f7a8e981-e779-4e27-8a12-64c3361ab93b';
    const EVENT_NAME = TypeOfEvent::EVENT_SCOPE_SET;






    const PARENT_CLASSES = [
        BaseEvent::class
    ];

}

