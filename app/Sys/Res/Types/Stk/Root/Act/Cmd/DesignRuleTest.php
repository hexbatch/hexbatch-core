<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Test a rule, or subtree
 * Can pick the set context and get a fake event if testing all the rule tree
 * No events generated outside this rule
 * No data changed
 */

class DesignRuleTest extends Act\Cmd
{
    const UUID = 'b568c6ea-842c-4dfa-994f-2ebbc7608d49';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_RULE_TEST;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

