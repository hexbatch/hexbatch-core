<?php

namespace App\Sys\Res\Types\Stk\Root\Remote;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Remote;

//this is the remote call made
class RemoteRules extends BaseType
{
    const UUID = '65f8a20b-6842-4486-957b-a1fa4c6d9bf8';
    const TYPE_NAME = 'remote_rules';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Remote::UUID
    ];

}

