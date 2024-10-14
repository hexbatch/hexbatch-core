<?php

namespace App\Sys\Res\Types\Stk\System\Act;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Action;


class Cmd extends BaseType
{
    const UUID = 'f4717906-b735-415d-80d0-6c17d4177595';
    const TYPE_NAME = 'command';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Action::UUID
    ];

}

