<?php

namespace App\Sys\Res\Elements\Stock\Remotes;


use App\Sys\Res\Elements\BaseElement;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stock\System\Remote\Outgoing\RemoteCacheSetType;
use App\Sys\Res\Types\Stock\System\Remote\RemoteSetType;

class RemoteCacheSetElement extends BaseElement
{
    const UUID = '57b137a8-2f69-446c-b4f8-b341c3addaad';
    const TYPE_UUID = RemoteCacheSetType::UUID;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

}


