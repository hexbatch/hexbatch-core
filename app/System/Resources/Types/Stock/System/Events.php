<?php

namespace App\System\Resources\Types\Stock\System;



use App\System\Resources\Attributes\Stock\System\Event\Scope\ChainScope;
use App\System\Resources\Attributes\Stock\System\Event\Scope\ElementScope;
use App\System\Resources\Attributes\Stock\System\Event\Scope\ServerScope;
use App\System\Resources\Attributes\Stock\System\Event\Scope\SetScope;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Events extends BaseType
{
    const UUID = '25df7e1f-7825-4528-b331-9e93d613a962';
    const TYPE_NAME = 'events';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        ElementScope::UUID,
        SetScope::UUID,
        ChainScope::UUID,
        ServerScope::UUID,
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

