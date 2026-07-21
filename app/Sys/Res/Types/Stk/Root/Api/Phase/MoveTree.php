<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Phase;

use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Act;

class MoveTree extends Api\PhaseApi
{
    const UUID = '8430647a-8d8c-4ba9-ab2d-c3b1c43013c5';
    const TYPE_NAME = 'api_phase_tree_move';





    const PARENT_CLASSES = [
        Api\PhaseApi::class,
        Act\Cmd\Ph\PhaseTreeMove::class
    ];

}

