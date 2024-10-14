<?php

namespace App\Sys\Res\Types\Stk\System\SystemNamespaceTypes\System\ThisServer\ThisServerNs;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Namespace\PrivateType;
use App\Sys\Res\Types\Stk\System\SystemNamespaceTypes\System\ThisServer\ThisServerNS;


class ThisServerPrivate extends BaseType
{
    const UUID = '11103578-95e2-4b32-b95d-f54f4cd8034d';
    const TYPE_NAME = 'system_private';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const PARENT_UUIDS = [
        ThisServerNS::UUID,
        PrivateType::UUID,
    ];

}

