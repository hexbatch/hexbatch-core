<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Atr\Stk\Act\Dbg\BaseDebug;
use App\Sys\Res\Atr\Stk\Act\Dbg\Logic;
use App\Sys\Res\Atr\Stk\Act\Dbg\Merge;
use App\Sys\Res\Types\BaseType;

/*
add new standard types for rendering rule_parts and path_parts: these have a standard attribute controlling the shape of the part.
Each rule and path part have standard attributes that match each internal db column
As elements, these can represent the rules and paths as chains of parent child sets, with each part sibling being in the same set.
Make new standard set for holding an element for each action, and a single element for the path part.
Adjust the color here in the set for rendering. When the debugger mode is used, this set->element rendering of the rule chains and paths are sent with the rest above

same for api calls as above, each of the api call types, defined already, has an element in the same standard set, where the color of shape of the api call is done

for visual debugging, there are new standard types for each of the logic , in the same way as above, and these elements are put into the set to adjust as needed.
the linkages in the tree use this logic element to help render

each logic, and each combo enum needs its own attribute for shape
 */

class Debug extends BaseType
{
    const UUID = '5f67d31d-acfa-4a20-87fe-427fd1a8d6bf';
    const TYPE_NAME = 'debug';



    const ATTRIBUTE_CLASSES = [
        BaseDebug::class,
        Logic\BaseLogic::class,
        Logic\LogicAnd::class,
        Logic\LogicFalse::class,
        Logic\LogicNop::class,
        Logic\LogicNand::class,
        Logic\LogicNor::class,
        Logic\LogicNorAll::class,
        Logic\LogicOr::class,
        Logic\LogicOrAll::class,
        Logic\LogicTrue::class,
        Logic\LogicXNor::class,
        Logic\LogicXor::class,

        Merge\BaseMerge::class,
        Merge\MergeAnd::class,
        Merge\MergeNewest::class,
        Merge\MergeOldest::class,
        Merge\MergeOr::class,
        Merge\MergeOverwrite::class,
        Merge\MergeXor::class,
    ];

    const PARENT_CLASSES = [
        Action::class
    ];

    public function isFinal(): bool { return true; } //just to organize the attributes

}

