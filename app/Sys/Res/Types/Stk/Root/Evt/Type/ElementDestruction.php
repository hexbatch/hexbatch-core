<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementDestruction extends Evt\ScopeSet
{
    const UUID = '2c1cb906-04a6-4f7c-aceb-abd9f9598ad7';
    const EVENT_NAME = TypeOfEvent::ELEMENT_DESTRUCTION;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

