<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class CustomEvent extends Evt\ScopeServer
{
    const UUID = '8d7dc80e-2e38-4652-a47e-ce88124a456a';
    const EVENT_NAME = TypeOfEvent::CUSTOM_EVENT;




    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

