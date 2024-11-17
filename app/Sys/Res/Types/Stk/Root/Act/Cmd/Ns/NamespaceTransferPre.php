<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class NamespaceTransferPre extends Act\Cmd\Ns
{
    const UUID = '57de6229-9f68-4bda-a1bf-613d06b742cd';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_PREP_TRANSFER;

    const ATTRIBUTE_CLASSES = [
        Metrics\NamespaceTransferPreMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\NamespaceStartingTransfer::class
    ];

}

