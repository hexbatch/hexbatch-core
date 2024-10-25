<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementTypeOn extends Evt\ScopeSet
{
    const UUID = '96b76741-e3ff-46ad-bf59-d05cc03366e8';
    const EVENT_NAME = TypeOfEvent::ELEMENT_TYPE_ON;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

