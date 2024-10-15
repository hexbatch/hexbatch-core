<?php

namespace App\Sys\Res\Types\Stk\System\Act;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;


class CmdNoEvents extends BaseAction
{
    const UUID = '19e3763f-9afa-4094-b6bb-67f26af2f1b7';
    const TYPE_NAME = 'command_no_events';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        BaseAction::UUID
    ];

}

