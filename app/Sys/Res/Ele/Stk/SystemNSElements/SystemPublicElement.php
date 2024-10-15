<?php

namespace App\Sys\Res\Ele\Stk\SystemNSElements;


use App\Sys\Res\Ele\BaseElement;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\SystemNamespaceTypes\System\ThisServer\ThisServerNs\ThisServerPrivate;

class SystemPublicElement extends BaseElement
{
    const UUID = '210b9154-bdbf-4337-a6b1-c221f42489ff';
    const TYPE_UUID = ThisServerPrivate::UUID;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

}


