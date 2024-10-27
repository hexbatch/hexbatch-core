<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\TypeOwnerPromoteMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class TypeOwnerPromote extends Act\Cmd
{
    const UUID = '8f4180e0-5ccc-4871-9a2a-8b1b22ae0e2a';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_OWNER_PROMOTE;

    const ATTRIBUTE_CLASSES = [
        TypeOwnerPromoteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class,
        Act\CmdNoEvents::class,
    ];

}

