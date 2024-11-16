<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Things make a child for each attribute to read and then join together in json in parent.
 * If any attribute cannot be read, then this action will fail
 */
class ReadType extends Act\Cmd\Ele
{
    const UUID = '32c7b4f1-7757-4a20-9a30-ef3b68b8dc81';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ_TYPE;

    const ATTRIBUTE_CLASSES = [
        Metrics\ReadTypeMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Pragma::class,
        Act\Cmd\Ele::class,
    ];

    const EVENT_CLASSES = [
        Evt\Set\Reading::class
    ];

}

