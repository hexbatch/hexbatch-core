<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignEdit extends Act\Cmd\Ds
{
    const UUID = '9f0285dc-0af5-4176-b82d-ac930d93b132';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_EDIT;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

