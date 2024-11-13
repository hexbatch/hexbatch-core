<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementDestructionBatch extends Evt\ScopeSet
{
    const UUID = '60d62ad8-e20e-49f8-9e9c-0f05c416b43c';
    const EVENT_NAME = TypeOfEvent::ELEMENT_DESTRUCTION_BATCH;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

