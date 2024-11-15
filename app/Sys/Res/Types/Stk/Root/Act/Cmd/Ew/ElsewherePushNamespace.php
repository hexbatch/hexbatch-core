<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


/**
 *
 */
class ElsewherePushNamespace extends Act\Cmd\Ew
{
    const UUID = '932ab22d-7502-438b-87fc-407002fa19f2';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_PUSH_NAMESPACE;

    const ATTRIBUTE_CLASSES = [
        Metrics\ElsewherePushNamespaceMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewherePushingNamespace::class
    ];

}

