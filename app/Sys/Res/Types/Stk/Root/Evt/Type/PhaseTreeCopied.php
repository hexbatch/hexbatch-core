<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * there was a phase edit action and the type was added into one or more places
 */
class PhaseTreeCopied extends Evt\ScopeType
{
    const UUID = '123230f6-cb59-4ca6-9c36-c9ec628511e0';
    const EVENT_NAME = TypeOfEvent::PHASE_TREE_COPIED;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

}

