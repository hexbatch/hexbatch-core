<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Remove a single live rule for the type
 */

class DesignLiveRuleRemove extends Act\Cmd
{
    const UUID = 'b3681a21-fa89-4bcb-9811-ee1f4cfd998a';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LIVE_RULE_REMOVE;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

