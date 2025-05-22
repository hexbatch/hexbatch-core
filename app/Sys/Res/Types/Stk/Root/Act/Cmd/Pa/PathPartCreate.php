<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class PathPartCreate extends Act\Cmd\Pa
{
    const UUID = 'e2fc566d-ba98-4852-9405-9482080b7efe';
    const ACTION_NAME = TypeOfAction::CMD_PATH_PART_CREATE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

