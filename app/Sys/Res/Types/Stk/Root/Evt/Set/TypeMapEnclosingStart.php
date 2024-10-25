<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeMapEnclosingStart extends Evt\ScopeSet
{
    const UUID = '034b22bd-71b5-416c-894d-eacd47c36b3a';
    const EVENT_NAME = TypeOfEvent::TYPE_MAP_ENCLOSING_START;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

