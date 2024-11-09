<?php

namespace App\Sys\Res\Types\Stk\Root\Phases;

use App\Sys\Res\ISystemResource;
use App\Sys\Res\Types\Stk\Root\Phase;


class NormalPhase extends Phase
{
    const UUID = 'cab35290-bb38-4bd9-a353-307b8779a6ac';
    const TYPE_NAME = 'normal_phase';

    const IS_DEFAULT_PHASE = true;

    const EDITED_BY_PHASE_SYSTEM_CLASS = EditPhase::class;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Phase::class
    ];

}

