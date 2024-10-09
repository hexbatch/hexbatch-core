<?php

namespace App\Sys\Res\Types\Stock;



use App\Sys\Res\Attributes\Stock\System\Event\Scope;
use App\Sys\Res\Attributes\Stock\System\System\Expiration;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class SystemType extends BaseType
{
    const UUID = '79a56b04-c36e-430f-bad4-5f53fb29ad4e';
    const TYPE_NAME = 'system';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        Expiration::UUID,
        Scope::UUID
    ];

    const PARENT_UUIDS = [];

}

