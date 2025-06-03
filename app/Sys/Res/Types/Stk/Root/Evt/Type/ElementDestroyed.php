<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementDestroyed extends Evt\ScopeType
{
    const UUID = 'a08204d3-b36b-44e9-a545-288d7da1bbd2';
    const EVENT_NAME = TypeOfEvent::ELEMENT_DESTRUCTION;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

