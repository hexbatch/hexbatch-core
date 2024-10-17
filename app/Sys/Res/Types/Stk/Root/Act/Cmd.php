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
        //todo each system action has its own attribute to use to identify itself in multiple actions in one element, for stats and rates in next layer
        // base one here and the others walk the inheritance structure
    ];

    const PARENT_UUIDS = [
        BaseAction::UUID
    ];

}

