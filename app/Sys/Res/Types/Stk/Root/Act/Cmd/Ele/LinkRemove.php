<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\LinkRemoveMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class LinkRemove extends Act\Cmd
{
    const UUID = 'c0f2f5b9-3030-4e60-9bd0-742299a6b83b';
    const ACTION_NAME = TypeOfAction::CMD_LINK_REMOVE;

    const ATTRIBUTE_CLASSES = [
        LinkRemoveMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

}

