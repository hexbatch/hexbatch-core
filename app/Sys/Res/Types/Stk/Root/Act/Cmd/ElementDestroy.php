<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * it is ok if this element is destroyed while things are working on it
 * it will just fail those things, or they will finish without it
 */
class ElementDestroy extends Act\Cmd
{
    const UUID = '557bbc2e-f589-4874-91f0-5d5e96fe115f';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_DESTROY;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}
