<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class UserDeletionStarting extends Evt\ScopeServer
{
    const UUID = '3cb134f3-3143-41b3-b929-08e1c240349d';
    const EVENT_NAME = TypeOfEvent::USER_DELETION_STARTING;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

