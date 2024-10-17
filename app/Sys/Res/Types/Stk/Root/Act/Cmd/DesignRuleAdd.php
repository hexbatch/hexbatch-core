<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Add a single rule or a tree to the attribute
 */

class DesignRuleAdd extends Act\Cmd
{
    const UUID = '32fdfc2b-33f6-4149-bec6-77a3dad30f1e';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_RULE_ADD;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

