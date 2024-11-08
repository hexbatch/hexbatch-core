<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Server;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ServerShowMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * asking elsewhere for new credentials
 */
class ServerShow extends Act\Cmd\Server
{
    const UUID = '0c3dd596-4636-4be2-9e89-00de6e97425b';
    const ACTION_NAME = TypeOfAction::CMD_SERVER_SHOW;

    const ATTRIBUTE_CLASSES = [
        ServerShowMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Server::class
    ];

}

