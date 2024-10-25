<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt\Element\ElementRecieved;
use App\Sys\Res\Types\Stk\Root\Evt\Element\ElementRecievedBatch;
use App\Sys\Res\Types\Stk\Root\Evt\Type\ElementOwnerChange;
use App\Sys\Res\Types\Stk\Root\Evt\Type\ElementOwnerChangeBatch;

/**
 * if no handler for element creation, then only the type owner members can create
 *
 * This can create one or many elements at once
 * it can access a list of ns from a child to create one element per ns. This can be any ns.
 *  if no list, then the caller will be the element owner
 *
 * Creation can be blocked by the following
 * @see ElementOwnerChange,ElementRecieved,ElementRecievedBatch,ElementOwnerChangeBatch
 *
 * it can access a list of sets from a child to create one per set (and put them in the set)
 *  if no set provided, it will put new element in the caller's home set.
 *  the set the element is going to will be provided as context info for any event handlers
 *
 * if more than one element created, the batch version of the handler is called instead
 *
 */

class ElementCreate extends Act\Cmd
{
    const UUID = 'c21c5d03-685f-467b-afce-3ec449197eda';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_CREATE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

