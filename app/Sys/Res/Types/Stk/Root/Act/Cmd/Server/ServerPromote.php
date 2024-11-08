<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Server;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ServerPromiteMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * asking elsewhere for new credentials
 */
class ServerPromote extends Act\Cmd\Server
{
    const UUID = '3fc91919-845c-4a9a-8261-db6de25db4b4';
    const ACTION_NAME = TypeOfAction::CMD_SERVER_PROMOTE;

    const ATTRIBUTE_CLASSES = [
        ServerPromiteMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Server::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

