<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class ReadLocation extends Act\Pragma
{
    const UUID = 'f15a18a6-dbc2-4642-a481-26ed8ccdda72';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ_LOCATION;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Pragma::class,
        Act\CmdNoSideEffects::class
    ];

}

