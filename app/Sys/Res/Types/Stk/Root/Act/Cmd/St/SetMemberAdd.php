<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\SetMemberAddMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class SetMemberAdd extends Act\Cmd\St
{
    const UUID = 'ebd1275e-ecc6-486e-89cb-69e14ae4a44c';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_ADD;

    const ATTRIBUTE_CLASSES = [
        SetMemberAddMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetEnter::class,
    ];

}

