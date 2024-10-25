<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class LinkDescriptionRemoved extends Evt\ScopeSet
{
    const UUID = '1d378fdc-305f-41c6-b2f2-0b92392d7fc8';
    const EVENT_NAME = TypeOfEvent::LINK_DESCRIPTION_REMOVED;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

