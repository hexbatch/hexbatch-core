<?php

namespace App\Sys\Res\Types\Stock\System\Placeholder\Namespace\LoggedIn;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Placeholder\Namespace\LoggedIn;


class PublicType extends BaseType
{
    const UUID = 'a960fb30-7351-4362-8a21-390ae3c85dcd';
    const TYPE_NAME = 'logged_in_public';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        LoggedIn::UUID,
        \App\Sys\Res\Types\Stock\System\Namespace\PublicType::UUID
    ];

}

