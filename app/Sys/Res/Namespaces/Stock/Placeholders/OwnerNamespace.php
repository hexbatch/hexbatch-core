<?php

namespace App\Sys\Res\Namespaces\Stock\Placeholders;


use App\Sys\Res\Ele\Stk\Holder\Owner\OwnerPrivateElement;
use App\Sys\Res\Ele\Stk\Holder\Owner\OwnerPublicElement;
use App\Sys\Res\Namespaces\BaseNamespace;

use App\Sys\Res\Sets\Stock\Placeholders\OwnerHomeSet;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Owner;

class OwnerNamespace extends BaseNamespace
{

    const UUID = 'fa864c3c-d358-4c40-88f1-e5ad2eb34b36';

    const TYPE_CLASS = Owner::class;
    const PUBLIC_ELEMENT_CLASS = OwnerPublicElement::class;
    const PRIVATE_ELEMENT_CLASS = OwnerPrivateElement::class;
    const HOMESET_CLASS = OwnerHomeSet::class;

    public static function getNamespaceName(): string
    {
        return 'owner_namespace_placeholder';
    }

}
