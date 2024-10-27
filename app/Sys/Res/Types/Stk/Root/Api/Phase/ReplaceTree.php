<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Phase;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ReplaceTree extends BaseType
{
    const UUID = '5e53b672-9a04-4e24-9dfb-3f08b646333d';
    const TYPE_NAME = 'api_phase_replace_tree';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PhaseApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\PhaseReplaceTree::class,
    ];

}

