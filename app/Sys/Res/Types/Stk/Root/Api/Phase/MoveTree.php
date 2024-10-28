<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Phase;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class MoveTree extends BaseType
{
    const UUID = 'a8a60e2d-4b5e-4560-b252-521fdef06d4b';
    const TYPE_NAME = 'api_phase_move_tree';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PhaseApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ph\PhaseMoveTree::class,
    ];

}

