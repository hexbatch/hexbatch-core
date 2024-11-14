<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Elsewhere;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * About to send new credentials to the elsewhere, can be cancelled
 */

class ElsewhereCredentialsSending extends Evt\ScopeElsewhere
{
    const UUID = '707b61a6-2080-4e1e-82d5-1df4effd2188';
    const EVENT_NAME = TypeOfEvent::ELSEWHERE_CREDENTIALS_SENDING;







    const PARENT_CLASSES = [
        Evt\ScopeElsewhere::class
    ];

}

