<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class AttributeDescriptionAdded extends Evt\ScopeSet
{
    const UUID = '2b4c800b-3716-41a9-9d0c-0e84516f06e2';
    const EVENT_NAME = TypeOfEvent::ATTRIBUTE_DESCRIPTION_ADDED;



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

