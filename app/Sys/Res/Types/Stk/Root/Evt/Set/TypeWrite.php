<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeWrite extends Evt\ScopeSet
{
    const UUID = 'cdb74907-00b5-405b-862e-f25d735a2cdc';
    const EVENT_NAME = TypeOfEvent::TYPE_WRITE;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

}

