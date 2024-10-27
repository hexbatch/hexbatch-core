<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\NamespaceMemberPurgeMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class NamespaceMemberPurge extends Act\Cmd
{
    const UUID = '071dd24b-9ce1-4ab9-9725-7cebd094fe02';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_MEMBER_PURGE;

    const ATTRIBUTE_CLASSES = [
        NamespaceMemberPurgeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class,
        Act\CmdNoEvents::class,
    ];

}

