<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Phase;

use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Act;

class Purge extends Api\PhaseApi
{
    const UUID = 'a85974f7-c1a1-4b5f-81cc-b8ae90539a99';
    const TYPE_NAME = 'api_phase_purge';





    const PARENT_CLASSES = [
        Api\PhaseApi::class,
        Act\Cmd\Ph\PhasePurge::class
    ];

}

