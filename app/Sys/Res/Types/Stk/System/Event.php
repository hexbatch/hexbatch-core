<?php

namespace App\Sys\Res\Types\Stk\System;



use App\Sys\Res\Atr\Stk\Event\Scope\ChainScope;
use App\Sys\Res\Atr\Stk\Event\Scope\ElementScope;
use App\Sys\Res\Atr\Stk\Event\Scope\ServerScope;
use App\Sys\Res\Atr\Stk\Event\Scope\SetScope;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Event extends BaseType
{
    const UUID = '25df7e1f-7825-4528-b331-9e93d613a962';
    const TYPE_NAME = 'events';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

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

