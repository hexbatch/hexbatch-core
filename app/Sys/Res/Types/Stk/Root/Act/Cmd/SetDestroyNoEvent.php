<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\SetDestroyNoEventMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class SetDestroyNoEvent extends Act\Cmd
{
    const UUID = 'd0d23dc0-d588-4a51-b10b-b2f3a8cfd49a';
    const ACTION_NAME = TypeOfAction::CMD_SET_DESTROY_NO_EVENT;

    const ATTRIBUTE_CLASSES = [
        SetDestroyNoEventMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class,
        Act\CmdNoEvents::class,
    ];

}

