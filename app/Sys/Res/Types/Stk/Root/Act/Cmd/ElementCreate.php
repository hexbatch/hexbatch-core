<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * if no handler for element creation, then only the type owner members can create
 *
 * This can create one or many elements at once
 * it can access a list of ns from a child to create one element per ns. This can be any ns.
 *  if no list, then the caller will be the element owner
 *
 * todo add event on the users private element on element_ownership (when a new or transferred ownership element is given to that ns)
 *  if that is rejected, then the element creation fails for that element, but if many are being created, the others can still be made
 *
 *
 * it can access a list of sets from a child to create one per set (and put them in the set)
 *  if no set provided, it will put new element in the caller home set
 *  the set the element is going to will be provided as context info for any event handlers
 *
 * if more than one element created, the batch version of the handler is called instead
 *
 */

class ElementCreate extends Act\Cmd
{
    const UUID = 'c21c5d03-685f-467b-afce-3ec449197eda';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_CREATE;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

