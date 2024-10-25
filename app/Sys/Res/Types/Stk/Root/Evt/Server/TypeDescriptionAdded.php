<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypeDescriptionAdded extends Evt\ScopeSet
{
    const UUID = 'a886f027-a54f-40fb-8049-d76624f6b5ca';
    const EVENT_NAME = TypeOfEvent::TYPE_DESCRIPTION_ADDED;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

