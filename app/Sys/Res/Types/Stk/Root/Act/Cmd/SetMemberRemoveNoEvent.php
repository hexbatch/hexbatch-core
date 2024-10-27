<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\SetMemberRemoveNoEventMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class SetMemberRemoveNoEvent extends Act\Cmd
{
    const UUID = '92b452a7-e0a7-4449-af30-8220f68ab70e';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_REMOVE_NO_EVENT;

    const ATTRIBUTE_CLASSES = [
        SetMemberRemoveNoEventMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

