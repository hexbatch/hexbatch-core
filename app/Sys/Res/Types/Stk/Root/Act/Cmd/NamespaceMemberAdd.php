<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\NamespaceMemberAddMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class NamespaceMemberAdd extends Act\Cmd
{
    const UUID = 'da5fd4af-adf2-4920-b03d-72660fadc4d1';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_MEMBER_ADD;

    const ATTRIBUTE_CLASSES = [
        NamespaceMemberAddMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

