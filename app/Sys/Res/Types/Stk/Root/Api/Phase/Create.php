<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Phase;

use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Act;

class Create extends Api\PhaseApi
{
    const UUID = '3e0c47d5-a6f1-46d6-8804-698d84a0a536';
    const TYPE_NAME = 'api_phase_create';





    const PARENT_CLASSES = [
        Api\PhaseApi::class,
        Act\Cmd\Ph\PhaseCreate::class
    ];

}

