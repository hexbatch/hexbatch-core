<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignAttributeHandle extends Act\Cmd
{
    const UUID = 'e9266b57-d485-438f-befe-7f5cd226643c';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_HANDLE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

