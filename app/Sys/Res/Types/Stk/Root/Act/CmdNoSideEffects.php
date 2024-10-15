<?php

namespace App\Sys\Res\Types\Stk\Root\Act;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;


class CmdNoSideEffects extends BaseAction
{
    const UUID = '42afd2f6-58f2-4236-b3bd-9ac517cd3a8b';
    const TYPE_NAME = 'command_no_side_effects';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        BaseAction::UUID
    ];

}

