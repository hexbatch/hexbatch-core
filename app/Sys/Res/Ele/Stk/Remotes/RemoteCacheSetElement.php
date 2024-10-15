<?php

namespace App\Sys\Res\Ele\Stk\Remotes;


use App\Sys\Res\Ele\BaseElement;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Remote\Outgoing\RemoteCacheSetType;

class RemoteCacheSetElement extends BaseElement
{
    const UUID = '57b137a8-2f69-446c-b4f8-b341c3addaad';
    const TYPE_UUID = RemoteCacheSetType::UUID;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

}


