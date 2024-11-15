<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Server;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ServerEditMetric;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * asking elsewhere for new credentials
 */
class ServerEdit extends Act\Cmd\Server
{
    const UUID = '880b0ed8-13ed-486e-ae85-f6e96d5fa681';
    const ACTION_NAME = TypeOfAction::CMD_SERVER_EDIT;

    const ATTRIBUTE_CLASSES = [
        ServerEditMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Server::class,
        Act\SystemPrivilege::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\ServerEdited::class,
    ];

}

