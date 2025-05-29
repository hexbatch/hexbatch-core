<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class LinkCreating extends Evt\ScopeServer
{
    const UUID = '22a1dfad-8550-468f-9288-84075af7cf2b';
    const EVENT_NAME = TypeOfEvent::LINK_CREATING;


    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

