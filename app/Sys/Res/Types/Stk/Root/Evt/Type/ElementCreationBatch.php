<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementCreationBatch extends Evt\ScopeSet
{
    const UUID = 'd995e77c-db66-4ab8-824e-3d511e5dea61';
    const EVENT_NAME = TypeOfEvent::ELEMENT_CREATION_BATCH;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

