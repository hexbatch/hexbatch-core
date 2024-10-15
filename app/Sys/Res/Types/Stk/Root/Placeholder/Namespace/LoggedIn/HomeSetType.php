<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\LoggedIn;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\LoggedIn;



class HomeSetType extends BaseType
{
    const UUID = 'b85b5a31-e922-485c-9351-5bd10fb6c6b9';
    const TYPE_NAME = 'logged_in_home_set';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        LoggedIn::UUID,
        \App\Sys\Res\Types\Stk\Root\Namespace\HomeSet::UUID
    ];

}

