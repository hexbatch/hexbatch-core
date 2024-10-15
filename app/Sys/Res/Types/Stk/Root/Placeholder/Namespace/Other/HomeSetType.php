<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Other;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Other;



class HomeSetType extends BaseType
{
    const UUID = '5b7567ec-65ff-499a-815c-800c839ce430';
    const TYPE_NAME = 'other_home_set';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Other::UUID,
        \App\Sys\Res\Types\Stk\Root\Namespace\HomeSet::UUID
    ];

}

