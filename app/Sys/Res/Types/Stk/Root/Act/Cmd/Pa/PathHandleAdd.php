<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PathHandleAddMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PathHandleAdd extends Act\Cmd\Pa
{
    const UUID = 'e39d9974-0cba-4366-aace-0e06bbcf629e';
    const ACTION_NAME = TypeOfAction::CMD_PATH_HANDLE_ADD;

    const ATTRIBUTE_CLASSES = [
        PathHandleAddMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

