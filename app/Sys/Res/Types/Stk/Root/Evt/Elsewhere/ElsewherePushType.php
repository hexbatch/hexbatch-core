<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewherePushType extends Evt\ScopeSet
{
    const UUID = '92245b59-df9a-4ad6-b9f6-fa21e1cfcb8f';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_PUSH_TYPE;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

