<?php

namespace App\Sys\Res\Types\Stk\Root\Act;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;


class Cmd extends BaseAction
{
    const UUID = 'f4717906-b735-415d-80d0-6c17d4177595';
    const TYPE_NAME = 'command';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        BaseAction::UUID
    ];

}

