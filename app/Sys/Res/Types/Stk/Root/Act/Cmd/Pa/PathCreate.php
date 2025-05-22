<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


class PathCreate extends Act\Cmd\Pa
{
    const UUID = '2334dec7-a4b9-4432-89fe-ac0e0078c29a';
    const ACTION_NAME = TypeOfAction::CMD_PATH_CREATE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

}

