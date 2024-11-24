<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class UserDeletionPreparing extends Evt\ScopeServer
{
    const UUID = '3faf234e-1eca-47f8-b915-4e823a91a305';
    const EVENT_NAME = TypeOfEvent::USER_DELETION_PREPARING;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

