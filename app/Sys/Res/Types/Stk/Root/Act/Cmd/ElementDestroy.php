<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * it is ok if this element is destroyed while things are working on it
 * it will just fail those things, or they will finish without it
 */
class ElementDestroy extends Act\Cmd
{
    const UUID = '557bbc2e-f589-4874-91f0-5d5e96fe115f';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_DESTROY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

