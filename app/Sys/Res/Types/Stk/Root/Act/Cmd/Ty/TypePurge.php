<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\TypePurgeMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class TypePurge extends Act\Cmd
{
    const UUID = 'bd328b7f-1cbb-49e9-a127-451834dd98e6';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_PURGE;


    const ATTRIBUTE_CLASSES = [
        TypePurgeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class,
        Act\CmdNoEvents::class,
    ];

}

