<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * todo is this event on the private home element in the default ns?
 */
class NamespaceCreated extends Evt\ScopeSet
{
    const UUID = '6ad6b92d-0cd0-4dd2-bc51-b2166e405a81';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_CREATED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

