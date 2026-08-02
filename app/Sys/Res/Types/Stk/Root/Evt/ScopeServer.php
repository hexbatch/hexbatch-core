<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;


use App\Enums\Sys\TypeOfEvent;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\UserNamespace;

class ScopeServer extends BaseEvent
{
    const UUID = '935a55bc-fbf9-4ffd-a6f5-63761e7c027e';
    const EVENT_NAME = TypeOfEvent::EVENT_SCOPE_SERVER;

    const ATTRIBUTE_CLASSES = [];
    const PARENT_CLASSES = [
        BaseEvent::class
    ];




}

