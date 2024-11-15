<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Server wants another server to have the element we gave them
 */
class ElsewhereSharingElement extends Act\Cmd\Ew
{
    const UUID = '5e15f311-4023-43b6-bb27-3c3e7b2badd3';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_SHARING_ELEMENT;

    const ATTRIBUTE_CLASSES = [
        Metrics\ElsewhereSharingElementMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereSharingElement::class
    ];

}

