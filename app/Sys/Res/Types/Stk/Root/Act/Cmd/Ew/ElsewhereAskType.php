<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * asking elsewhere for new credentials
 *
 *  will also @uses ElsewherePushCredentials if ok to send
 *
 */
class ElsewhereAskType extends Act\Cmd\Ew
{
    const UUID = 'a84268b0-bb6f-4a2e-ac5c-f34ea1114b96';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_ASK_TYPE;

    const ATTRIBUTE_CLASSES = [
        Metrics\ElsewhereAskTypeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereAskingType::class,
    ];

}

