<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Phase;

use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Act;

class DeleteTree extends Api\PhaseApi
{
    const UUID = 'c763cfe6-702b-455a-a78b-50bd193e259d';
    const TYPE_NAME = 'api_phase_tree_delete';





    const PARENT_CLASSES = [
        Api\PhaseApi::class,
        Act\Cmd\Ph\PhaseTreeDelete::class
    ];

}

