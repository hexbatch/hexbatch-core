<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\NamespaceCreateMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class NamespaceCreate extends Act\Cmd\Ns
{
    const UUID = '2eb062ae-f06e-4b01-8a9f-2059f2fbc40b';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_CREATE;

    const ATTRIBUTE_CLASSES = [
        NamespaceCreateMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\NamespaceCreated::class
    ];

}

