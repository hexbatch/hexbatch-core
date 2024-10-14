<?php

namespace App\Sys\Res\Types\Stk\System\Placeholder\Namespace;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Namespace\BasePerNamespace;
use App\Sys\Res\Types\Stk\System\Placeholder;


class LoggedIn extends BaseType
{
    const UUID = '8e69a890-ac55-451a-971e-ca9b0f9357e2';
    const TYPE_NAME = 'logged_in';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Placeholder\CurrentNamespace::UUID,
        BasePerNamespace::UUID
    ];

}

