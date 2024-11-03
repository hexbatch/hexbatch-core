<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Phase;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class CutTree extends Api\PhaseApi
{
    const UUID = '15fa891f-1085-4289-85d1-181f2e7416d6';
    const TYPE_NAME = 'api_phase_cut_tree';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PhaseApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ph\PhaseCutTree::class,
    ];

}

