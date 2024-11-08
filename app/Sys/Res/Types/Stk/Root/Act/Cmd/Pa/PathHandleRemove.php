<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PathHandleRemoveMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PathHandleRemove extends Act\Cmd\Pa
{
    const UUID = '34166e12-434b-4b71-ba07-03595e5082ef';
    const ACTION_NAME = TypeOfAction::CMD_PATH_HANDLE_REMOVE;

    const ATTRIBUTE_CLASSES = [
        PathHandleRemoveMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

