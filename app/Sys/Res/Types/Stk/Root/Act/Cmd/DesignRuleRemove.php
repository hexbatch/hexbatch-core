<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Remove a single rule or subtree tree from the attribute
 */

class DesignRuleRemove extends Act\Cmd
{
    const UUID = '49d036b2-9f53-4fad-afed-b7d628ac060c';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_RULE_REMOVE;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

