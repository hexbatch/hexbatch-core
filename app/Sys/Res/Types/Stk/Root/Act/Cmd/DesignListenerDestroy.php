<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Destroy the listening event from the attribute
 */
class DesignListenerDestroy extends Act\Cmd
{
    const UUID = 'a4fc0537-f43e-461e-9be4-0918f2ec0542';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LISTENER_DESTROY;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}
