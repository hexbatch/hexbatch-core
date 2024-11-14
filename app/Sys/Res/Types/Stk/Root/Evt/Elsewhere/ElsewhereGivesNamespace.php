<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereGivesNamespace extends Evt\ScopeElsewhere
{
    const UUID = '9de6ba2c-3cc3-40ac-a490-79a50d021e09';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_GIVES_NAMESPACE;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

