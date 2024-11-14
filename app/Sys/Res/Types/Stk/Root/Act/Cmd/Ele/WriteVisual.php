<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\WriteVisualMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class WriteVisual extends Act\Cmd\Ele
{
    const UUID = '8d357b98-64e5-4e90-bcab-ae24d6bff07c';
    const ACTION_NAME = TypeOfAction::PRAGMA_WRITE_VISUAL;

    const ATTRIBUTE_CLASSES = [
        WriteVisualMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\Pragma::class
    ];

    const EVENT_CLASSES = [
        Evt\Element\WritingVisual::class
    ];

}

