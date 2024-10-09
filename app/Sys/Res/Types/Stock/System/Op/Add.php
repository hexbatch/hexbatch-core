<?php

namespace App\Sys\Res\Types\Stock\System\Op;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\GroupOperation;


class Add extends BaseType
{
    const UUID = 'c4f79042-3be1-4c9a-9342-235341d5f0d0';
    const TYPE_NAME = 'group_operation_add';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        GroupOperation::UUID
    ];

}

