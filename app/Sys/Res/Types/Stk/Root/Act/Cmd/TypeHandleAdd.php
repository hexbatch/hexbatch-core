<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * types which share the same handle are published and have other lifecycle changes, done  in a group,
 * if any type fails to change, then none do
 */
class TypeHandleAdd extends Act\Cmd
{
    const UUID = 'c79c2c70-f92e-4fdc-9cff-db756f8a1c8b';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_HANDLE_ADD;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

