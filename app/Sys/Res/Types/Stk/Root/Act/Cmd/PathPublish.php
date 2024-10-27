<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\PathPublishMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class PathPublish extends Act\Cmd
{
    const UUID = 'f329ac05-5474-4050-9f1c-ef2e6b8b065f';
    const ACTION_NAME = TypeOfAction::CMD_PATH_PUBLISH;

    const ATTRIBUTE_CLASSES = [
        PathPublishMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

