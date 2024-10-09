<?php

namespace App\Sys\Res\Types\Stock\System\Event\Server;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Event;


class RunRemote extends BaseType
{
    const UUID = '09e8b85d-4b1d-4500-ab32-f8ae03dd1af9';
    const TYPE_NAME = 'event_run_remote';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Event\ScopeServer::UUID
    ];

}

