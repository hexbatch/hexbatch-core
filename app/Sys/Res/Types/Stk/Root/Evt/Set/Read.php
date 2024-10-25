<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class Read extends Evt\ScopeSet
{
    const UUID = '333a57fc-8472-4d88-b69e-a63ac64fe642';
    const EVENT_NAME = TypeOfEvent::ATTRIBUTE_READ;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

