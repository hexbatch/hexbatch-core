<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class ElsewhereGiveNamespace extends Act\Cmd\Ew
{
    const UUID = '3fa65eaf-79c0-4097-89aa-84d4c4643215';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_GIVE_NS;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereGivesNamespace::class
    ];

}

