<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ElsewhereGiveSetMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class ElsewhereGiveSet extends Act\Cmd
{
    const UUID = '14cebbf0-7d15-4304-9da6-8107f91c8211';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_GIVE_SET;

    const ATTRIBUTE_CLASSES = [
        ElsewhereGiveSetMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

