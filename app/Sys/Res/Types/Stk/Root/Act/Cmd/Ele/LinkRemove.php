<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class LinkRemove extends LinkAdd
{
    const UUID = 'c0f2f5b9-3030-4e60-9bd0-742299a6b83b';
    const ACTION_NAME = TypeOfAction::CMD_LINK_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\LinkDestroyed::class
    ];

    const EVENT_CLASS = Evt\Server\LinkDestroyed::class;

    const bool IS_ADDING = false;

}

