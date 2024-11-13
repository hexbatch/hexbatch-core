<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Gets a new key to use for elsewhere
 */

class ElsewhereCredentialsBad extends Evt\ScopeSet
{
    const UUID = '40543a62-59cf-4db0-ab64-a4aa1cf42ea7';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_CREDENTIALS_BAD;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

