<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class NamespaceHandleRemove extends Act\Cmd\Ns
{
    const UUID = 'd8b51500-107d-4cd1-99b4-9efe283f8903';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_HANDLE_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\NamespaceHandleRemoved::class,
    ];

}

