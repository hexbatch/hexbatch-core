<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementCreation extends Evt\ScopeSet
{
    const UUID = '41d42dcb-2429-4183-82d5-7c3a04a36a1b';
    const EVENT_NAME = TypeOfEvent::ELEMENT_CREATION;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

