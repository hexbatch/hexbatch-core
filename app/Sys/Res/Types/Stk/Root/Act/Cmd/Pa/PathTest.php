<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class PathTest extends Act\Cmd\Pa
{
    const UUID = '617b55ef-8f66-41e1-bfe6-588aa8098d71';
    const ACTION_NAME = TypeOfAction::CMD_PATH_TEST;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

