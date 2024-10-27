<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\NamespaceAdminPurgeMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class NamespaceAdminPurge extends Act\Cmd
{
    const UUID = '2812f86f-ff40-4617-bc51-6ee5184492c3';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_ADMIN_PURGE;

    const ATTRIBUTE_CLASSES = [
        NamespaceAdminPurgeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class,
        Act\CmdNoEvents::class,
    ];

}

