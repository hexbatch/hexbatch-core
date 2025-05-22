<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class PathCopy extends Act\Cmd\Pa
{
    const UUID = 'a72706e2-ccb2-49a1-b890-5afecd51f219';
    const ACTION_NAME = TypeOfAction::CMD_PATH_COPY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

