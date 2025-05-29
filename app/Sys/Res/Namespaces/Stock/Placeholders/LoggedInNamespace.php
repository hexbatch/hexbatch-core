<?php

namespace App\Sys\Res\Namespaces\Stock\Placeholders;



use App\Sys\Res\Ele\Stk\Holder\LoggedIn\LoggedInPrivateElement;
use App\Sys\Res\Ele\Stk\Holder\LoggedIn\LoggedInPublicElement;
use App\Sys\Res\Namespaces\BaseNamespace;

use App\Sys\Res\Sets\Stock\Placeholders\LoggedInHomeSet;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\LoggedIn;

class LoggedInNamespace extends BaseNamespace
{

    const UUID = '89cd1314-7f70-45c9-afd6-719f890486a3';

    const TYPE_CLASS = LoggedIn::class;
    const PUBLIC_ELEMENT_CLASS = LoggedInPublicElement::class;
    const PRIVATE_ELEMENT_CLASS = LoggedInPrivateElement::class;
    const HOMESET_CLASS = LoggedInHomeSet::class;

    public static function getNamespaceName(): string
    {
        return 'placeholder_current_namespace';
    }

    public static function getClassUuid(): string { return static::UUID; }
}
