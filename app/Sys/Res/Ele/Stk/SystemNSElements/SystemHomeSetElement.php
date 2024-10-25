<?php

namespace App\Sys\Res\Ele\Stk\SystemNSElements;


use App\Sys\Res\Ele\BaseElement;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\SystemNamespaceTypes\System\ThisServer\ThisServerNs\ThisServerHomeset;

class SystemHomeSetElement extends BaseElement
{
    const UUID = '42db7fa3-e3fd-4b9f-baf1-0aafc800b5b0';
    const TYPE_CLASS = ThisServerHomeset::class;
    const NAMESPACE_CLASS = ThisServerNamespace::class;

}


