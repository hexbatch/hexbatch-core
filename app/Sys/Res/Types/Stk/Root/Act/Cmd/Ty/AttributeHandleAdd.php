<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\AttributeHandleAddMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class AttributeHandleAdd extends Act\Cmd
{
    const UUID = '8a3fbc96-6772-493e-937d-e0306fa46fbc';
    const ACTION_NAME = TypeOfAction::CMD_ATTRIBUTE_HANDLE_ADD;

    const ATTRIBUTE_CLASSES = [
        AttributeHandleAddMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

}

