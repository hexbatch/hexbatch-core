<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignAttributeEdit extends Act\Cmd
{
    const UUID = 'b5dc244c-d966-48fd-9c42-ed53cceb827f';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_EDIT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

