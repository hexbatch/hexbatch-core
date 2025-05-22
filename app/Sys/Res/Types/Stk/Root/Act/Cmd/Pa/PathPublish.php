<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class PathPublish extends Act\Cmd\Pa
{
    const UUID = 'f329ac05-5474-4050-9f1c-ef2e6b8b065f';
    const ACTION_NAME = TypeOfAction::CMD_PATH_PUBLISH;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

