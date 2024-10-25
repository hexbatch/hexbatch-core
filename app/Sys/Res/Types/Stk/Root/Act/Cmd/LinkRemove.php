<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class LinkRemove extends Act\Cmd
{
    const UUID = 'c0f2f5b9-3030-4e60-9bd0-742299a6b83b';
    const ACTION_NAME = TypeOfAction::CMD_LINK_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

