<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class UserEdit extends Evt\ScopeServer
{
    const UUID = '621ce1ca-a944-4850-b7f5-c2dff6bb77cb';
    const EVENT_NAME = TypeOfEvent::USER_EDIT;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

