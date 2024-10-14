<?php

namespace App\Sys\Res\Types\Stock\System\SystemNamespaceTypes\System\ThisServer\ThisServerNs;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Namespace\HomeSet;
use App\Sys\Res\Types\Stock\System\SystemNamespaceTypes\System\ThisServer\ThisServerNS;


class ThisServerHomeset extends BaseType
{
    const UUID = 'ba503fc2-53c1-4d56-940e-d16efdd1e9c1';
    const TYPE_NAME = 'system_homeset';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const PARENT_UUIDS = [
        ThisServerNS::UUID,
        HomeSet::UUID
    ];

}

