<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\AttributeHandleRemoveMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class AttributeHandleRemove extends Act\Cmd
{
    const UUID = '3618e198-e428-45ac-94e7-7aac4d1e8f85';
    const ACTION_NAME = TypeOfAction::CMD_ATTRIBUTE_HANDLE_REMOVE;

    const ATTRIBUTE_CLASSES = [
       AttributeHandleRemoveMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

