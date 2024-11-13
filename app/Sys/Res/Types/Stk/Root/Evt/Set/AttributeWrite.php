<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class AttributeWrite extends Evt\ScopeSet
{
    const UUID = 'a1b06d04-7ac4-43a1-8353-a3f9c7df1b94';
    const EVENT_NAME = TypeOfEvent::ATTRIBUTE_WRITE;




    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

