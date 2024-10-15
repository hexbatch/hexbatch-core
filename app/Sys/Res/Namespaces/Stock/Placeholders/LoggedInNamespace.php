<?php

namespace App\Sys\Res\Namespaces\Stock\Placeholders;



use App\Sys\Res\Ele\Stk\Placeholders\LoggedIn\LoggedInPrivateElement;
use App\Sys\Res\Ele\Stk\Placeholders\LoggedIn\LoggedInPublicElement;
use App\Sys\Res\Namespaces\BaseNamespace;
use App\Sys\Res\Servers\Stock\ThisServer;

use App\Sys\Res\Sets\Stock\Placeholders\LoggedInHomeSet;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\LoggedIn;

use App\Sys\Res\Users\Stock\SystemUser;

class LoggedInNamespace extends BaseNamespace
{

    const UUID = '89cd1314-7f70-45c9-afd6-719f890486a3';

    const TYPE_UUID = LoggedIn::UUID;
    const PUBLIC_ELEMENT_UUID = LoggedInPublicElement::UUID;
    const PRIVATE_ELEMENT_UUID = LoggedInPrivateElement::UUID;
    const HOMESET_UUID = LoggedInHomeSet::UUID;
    const SERVER_UUID = ThisServer::UUID;
    const USER_UUID = SystemUser::UUID;

    public function getNamespaceName(): string
    {
        return 'logged_in_namespace_placeholder';
    }

    public function getNamespacePublicKey(): ?string
    {
       return null;
    }
}
