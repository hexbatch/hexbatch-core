<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
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

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

