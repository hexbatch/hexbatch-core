<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class TypeRetire extends Act\Cmd\Ty
{
    const UUID = '4e99584e-0e22-4db6-a5c0-b99d1caa9c04';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_RETIRE;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\TypeRetired::class
    ];

}

