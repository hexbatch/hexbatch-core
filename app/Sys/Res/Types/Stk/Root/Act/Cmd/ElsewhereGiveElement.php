<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class ElsewhereGiveElement extends Act\Cmd
{
    const UUID = 'b916d43d-6573-4d94-84e7-f634c227af91';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_GIVE_ELEMENT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

