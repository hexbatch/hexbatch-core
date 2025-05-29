<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * We got a new way to login from elsewhere
 */

class ElsewhereCredentialsNew extends Evt\ScopeElsewhere
{
    const UUID = '1f3be86e-9d29-4e8d-992e-23edad86dcb5';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_CREDENTIALS_NEW;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

