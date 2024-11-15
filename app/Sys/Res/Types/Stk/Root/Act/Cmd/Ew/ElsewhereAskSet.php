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
class ElsewhereAskSet extends Act\Cmd\Ew
{
    const UUID = '91a1fecd-f40b-441f-b550-60e742bdd618';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_ASK_SET;

    const ATTRIBUTE_CLASSES = [
        Metrics\ElsewhereAskSetMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereAskingSet::class,
    ];

}

