<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class PathHandleAdd extends Act\Cmd
{
    const UUID = 'e39d9974-0cba-4366-aace-0e06bbcf629e';
    const ACTION_NAME = TypeOfAction::CMD_PATH_HANDLE_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

