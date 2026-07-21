<?php

namespace App\Sys\Res\Types\Stk\Root\Phases;

use App\Sys\Res\Types\Stk\Root\Phase;


class EditPhase extends Phase
{
    const UUID = '0174da15-5543-4f34-a844-c00689f87156';
    const TYPE_NAME = 'edit_phase';
    const EDITED_BY_PHASE_SYSTEM_CLASS = AdvicePhase::class;


    const PHASE_UUID = 'c2354914-f726-4733-ad81-995c2ac5e634';

    const PARENT_CLASSES = [
        Phase::class
    ];

}

