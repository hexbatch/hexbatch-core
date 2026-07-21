<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * When new type published, and new row is created in @see \App\Models\Phase
 * There is not a command to create a phase directly
 * When the type is destroyed, that corresponding row in the phase is destroyed
 */
class Phase extends BaseType
{
    const UUID = '1bb5ff53-6874-4914-afd9-4dc8c9534c8f';
    const TYPE_NAME = 'phase';

    const PHASE_UUID = null;

    const EDITED_BY_PHASE_SYSTEM_CLASS = '';

    const IS_DEFAULT_PHASE = false;


    protected \App\Models\Phase|null $phase = null;



    const PARENT_CLASSES = [
        Root::class
    ];




}

