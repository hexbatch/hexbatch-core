<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ReadTimeSpanMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class ReadTimeSpan extends Act\Pragma
{
    const UUID = '7aca447b-968d-455c-8fc9-8c4705f89771';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ_TIME_SPAN;

    const ATTRIBUTE_CLASSES = [
        ReadTimeSpanMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Pragma::class,
        Act\CmdNoSideEffects::class
    ];

}

