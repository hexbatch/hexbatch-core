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
class ElsewhereAskElement extends Act\Cmd\Ew
{
    const UUID = '479b450e-1c80-42f9-a9d6-d374bdcf4ad6';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_ASK_ELEMENT;

    const ATTRIBUTE_CLASSES = [
        Metrics\ElsewhereAskElementMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereAskingElement::class,
    ];

}

