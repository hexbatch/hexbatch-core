<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class PathPartEdit extends Act\Cmd\Pa
{
    const UUID = '85821ebb-b594-458c-b3ba-b5e45689bd2a';
    const ACTION_NAME = TypeOfAction::CMD_PATH_PART_EDIT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

