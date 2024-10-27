<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\TypeChangeOwnerMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class TypeChangeOwner extends Act\Cmd
{
    const UUID = '997f8aba-30a0-4b14-a75e-c64dac02e85b';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_CHANGE_OWNER;


    const ATTRIBUTE_CLASSES = [
        TypeChangeOwnerMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

