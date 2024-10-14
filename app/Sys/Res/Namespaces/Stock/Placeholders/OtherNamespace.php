<?php

namespace App\Sys\Res\Namespaces\Stock\Placeholders;


use App\Sys\Res\Elements\Stock\Placeholders\Other\OtherPrivateElement;
use App\Sys\Res\Elements\Stock\Placeholders\Other\OtherPublicElement;
use App\Sys\Res\Namespaces\BaseNamespace;
use App\Sys\Res\Servers\Stock\ThisServer;

use App\Sys\Res\Sets\Stock\Placeholders\OtherHomeSet;
use App\Sys\Res\Types\Stock\System\Placeholder\Namespace\Other;


use App\Sys\Res\Users\Stock\SystemUser;

class OtherNamespace extends BaseNamespace
{

    const UUID = '5c87c57b-9a3f-4ba0-a31f-a5de9999ebec';

    const TYPE_UUID = Other::UUID;
    const PUBLIC_ELEMENT_UUID = OtherPublicElement::UUID;
    const PRIVATE_ELEMENT_UUID = OtherPrivateElement::UUID;
    const HOMESET_UUID = OtherHomeSet::UUID;
    const SERVER_UUID = ThisServer::UUID;
    const USER_UUID = SystemUser::UUID;

    public function getNamespaceName(): string
    {
        return 'other_namespace_placeholder';
    }

    public function getNamespacePublicKey(): ?string
    {
       return null;
    }
}
