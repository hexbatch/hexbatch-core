<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\TypeOwnerChangeMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class TypeOwnerChange extends Act\Cmd\Ty
{
    const UUID = '997f8aba-30a0-4b14-a75e-c64dac02e85b';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_OWNER_CHANGE;


    const ATTRIBUTE_CLASSES = [
        TypeOwnerChangeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

}

