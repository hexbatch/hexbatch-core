<?php

namespace App\Sys\Res\Types\Stk;



use App\Sys\Res\Atr\Stk\Event\Scope;
use App\Sys\Res\Atr\Stk\System\Expiration;
use App\Sys\Res\Atr\Stk\System\OutsideUrl;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Root extends BaseType
{
    const UUID = '79a56b04-c36e-430f-bad4-5f53fb29ad4e';
    const TYPE_NAME = 'system';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        Expiration::UUID,
        OutsideUrl::UUID,
        Scope::UUID
    ];
    //todo add root attribute as parent for all attributes, this root attribute has its own uuid from .env or make one

    const PARENT_UUIDS = [];

}

