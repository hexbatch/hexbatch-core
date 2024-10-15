<?php

namespace App\Sys\Res\Types\Stk\Root\SystemNamespaceTypes\System\ThisServer\ThisServerNs;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\BasePerNamespace;
use App\Sys\Res\Types\Stk\Root\Namespace\Description;


class ThisServerDescription extends BaseType
{
    const UUID = 'ec0e3d19-beee-4e67-832b-cfcf7f93df22';
    const TYPE_NAME = 'system_description';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        BasePerNamespace::UUID,
        Description::UUID
    ];

}

