<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Phase;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Purge extends Api\PhaseApi
{
    const UUID = '2ed3fc44-123d-45eb-be51-8cdf523aab02';
    const TYPE_NAME = 'api_phase_purge';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PhaseApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ph\PhasePurge::class,
    ];

}

