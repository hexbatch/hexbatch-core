<?php

namespace App\Sys\Res\Types\Stk\Root\Phases;

use App\Sys\Res\Types\Stk\Root\Phase;


class AdvicePhase extends Phase
{
    const UUID = '8139a602-d6fe-4a46-9b90-dd8c1521ad22';
    const TYPE_NAME = 'advice_phase';
    const EDITED_BY_PHASE_SYSTEM_CLASS = NormalPhase::class;




    const PARENT_CLASSES = [
        Phase::class
    ];

}

