<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\TypeHandleRemoveMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class TypeHandleRemove extends Act\Cmd\Ty
{
    const UUID = 'bc4a52eb-7108-4e2f-82cb-a2468b9edd06';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_HANDLE_REMOVE;

    const ATTRIBUTE_CLASSES = [
        TypeHandleRemoveMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\TypeHandleRemoved::class
    ];

}

