<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class LinkCreated extends Evt\ScopeSet
{
    const UUID = 'b1c70fce-690b-418f-827d-982f6d84e256';
    const EVENT_NAME = TypeOfEvent::LINK_CREATED;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

