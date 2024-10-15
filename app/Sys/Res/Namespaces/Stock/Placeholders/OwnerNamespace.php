<?php

namespace App\Sys\Res\Namespaces\Stock\Placeholders;


use App\Sys\Res\Ele\Stk\Placeholders\Owner\OwnerPrivateElement;
use App\Sys\Res\Ele\Stk\Placeholders\Owner\OwnerPublicElement;
use App\Sys\Res\Namespaces\BaseNamespace;
use App\Sys\Res\Servers\Stock\ThisServer;

use App\Sys\Res\Sets\Stock\Placeholders\OwnerHomeSet;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Owner;

use App\Sys\Res\Users\Stock\SystemUser;

class OwnerNamespace extends BaseNamespace
{

    const UUID = 'fa864c3c-d358-4c40-88f1-e5ad2eb34b36';

    const TYPE_UUID = Owner::UUID;
    const PUBLIC_ELEMENT_UUID = OwnerPublicElement::UUID;
    const PRIVATE_ELEMENT_UUID = OwnerPrivateElement::UUID;
    const HOMESET_UUID = OwnerHomeSet::UUID;
    const SERVER_UUID = ThisServer::UUID;
    const USER_UUID = SystemUser::UUID;

    public function getNamespaceName(): string
    {
        return 'owner_namespace_placeholder';
    }

    public function getNamespacePublicKey(): ?string
    {
       return null;
    }
}
