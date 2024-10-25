<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class TypeRetire extends Act\Cmd
{
    const UUID = '4e99584e-0e22-4db6-a5c0-b99d1caa9c04';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_RETIRE;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

