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
class ElsewhereAskNamespace extends Act\Cmd\Ew
{
    const UUID = '736adcea-b851-4454-b3c9-69fe81d273c7';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_ASK_NAMESPACE;

    const ATTRIBUTE_CLASSES = [
        Metrics\ElsewhereAskNamespaceMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereAskingNamespace::class,
    ];

}

