<?php

namespace App\Sys\Res\Namespaces\Stock\Placeholders;


use App\Sys\Res\Ele\Stk\Placeholders\Other\OtherPrivateElement;
use App\Sys\Res\Ele\Stk\Placeholders\Other\OtherPublicElement;
use App\Sys\Res\Namespaces\BaseNamespace;

use App\Sys\Res\Sets\Stock\Placeholders\OtherHomeSet;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Other;


class OtherNamespace extends BaseNamespace
{

    const UUID = '5c87c57b-9a3f-4ba0-a31f-a5de9999ebec';

    const TYPE_CLASS = Other::class;
    const PUBLIC_ELEMENT_CLASS = OtherPublicElement::class;
    const PRIVATE_ELEMENT_CLASS = OtherPrivateElement::class;
    const HOMESET_CLASS = OtherHomeSet::class;

    public function getNamespaceName(): string
    {
        return 'other_namespace_placeholder';
    }

    public function getNamespacePublicKey(): ?string
    {
       return null;
    }
}
