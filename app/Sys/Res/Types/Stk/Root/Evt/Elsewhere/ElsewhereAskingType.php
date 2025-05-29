<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereAskingType extends Evt\ScopeElsewhere
{
    const UUID = '83dd31b0-8dab-4b0f-a319-b5bb9fddbca8';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_ASKING_TYPE;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

