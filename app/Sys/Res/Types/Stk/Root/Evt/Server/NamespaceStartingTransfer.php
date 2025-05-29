<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class NamespaceStartingTransfer extends Evt\ScopeServer
{
    const UUID = 'a49e7a9c-15eb-4d1d-9f5e-b0d1bb37e9eb';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_STARTING_TRANSFER;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

