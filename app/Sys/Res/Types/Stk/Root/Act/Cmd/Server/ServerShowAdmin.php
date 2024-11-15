<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Server;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * asking elsewhere for new credentials
 */
class ServerShowAdmin extends Act\Cmd\Server
{
    const UUID = 'e2fa7474-a162-4fbe-9065-72be16811d97';
    const ACTION_NAME = TypeOfAction::CMD_SERVER_SHOW_ADMIN;

    const ATTRIBUTE_CLASSES = [
        Metrics\ServerShowAdminMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Server::class
    ];

}

