<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class NamespaceDestroy extends Act\Cmd\Ns
{
    const UUID = '0253a9c0-78db-4f8d-b648-7d2abd5ac47c';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_DESTROY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\NamespaceDestroyed::class
    ];

}

