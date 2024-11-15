<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereAskingSet extends Evt\ScopeElsewhere
{
    const UUID = 'ebaa9290-fd7f-4dd8-b135-43ae9a65e041';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_ASKING_SET;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

