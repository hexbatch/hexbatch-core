<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt\Set\Write;

/**
 * if there is one or more elements who have a matching live type (or base live type)
 * then pick at most one element and write matching attributes to that live type
 *
 * The pinged element does not become a member of the set
 * there is only a @see Write event on the type
 * and no set events
 *
 * if ping a set, and no match, nothing happens
 */

class ElementPingToSet extends Act\Cmd
{
    const UUID = '54e60992-c545-4280-9469-b1c02e0f6fc5';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_PING_TO_SET;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

