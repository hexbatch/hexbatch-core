<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementCreation extends Evt\ScopeType
{
    const UUID = '41d42dcb-2429-4183-82d5-7c3a04a36a1b';
    const EVENT_NAME = TypeOfEvent::ELEMENT_CREATION;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];




    public function canCreate(): bool
    {
        return true;  //todo this is stubbed
    }

}

