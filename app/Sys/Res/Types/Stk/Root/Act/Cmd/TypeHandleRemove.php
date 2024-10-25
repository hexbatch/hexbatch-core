<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class TypeHandleRemove extends Act\Cmd
{
    const UUID = 'bc4a52eb-7108-4e2f-82cb-a2468b9edd06';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_HANDLE_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

