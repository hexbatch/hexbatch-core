<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class DesignPending extends Evt\ScopeServer
{
    const UUID = 'be5621ec-355d-48c4-a838-a3e0735fb3af';
    const EVENT_NAME = TypeOfEvent::DESIGN_PENDING;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

