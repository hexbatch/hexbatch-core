<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class MapDisplayWrite extends Evt\ScopeElement
{
    const UUID = '4a070471-0924-4a27-8ea0-3dbaf2e32a8e';
    const EVENT_NAME = TypeOfEvent::MAP_DISPLAY_WRITE;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

