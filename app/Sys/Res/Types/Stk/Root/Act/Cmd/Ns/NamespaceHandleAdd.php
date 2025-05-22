<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class NamespaceHandleAdd extends Act\Cmd\Ns
{
    const UUID = '9ab8300d-01a6-4d7e-bbb5-f54cb93b3232';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_HANDLE_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Pa::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\NamespaceHandleAdded::class,
    ];

}

