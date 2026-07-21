<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Phase;

use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Act;

class CopyTree extends Api\PhaseApi
{
    const UUID = '12589fae-881c-4c5a-bbca-e7c719982e2c';
    const TYPE_NAME = 'api_phase_tree_copy';





    const PARENT_CLASSES = [
        Api\PhaseApi::class,
        Act\Cmd\Ph\PhaseTreeCopy::class
    ];

}

