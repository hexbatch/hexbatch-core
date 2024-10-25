<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class NamespaceDestroyNoEvent extends Act\Cmd
{
    const UUID = '89059226-b860-4d62-9ff9-1d88a4b7037a';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_DESTROY_NO_EVENT;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class,
        Act\CmdNoEvents::class,
    ];

}

