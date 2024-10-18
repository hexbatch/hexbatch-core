<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * Increases the charge by +1  to the live attribute, the collection of charges can select the live type being added
 * no events raised, this affects future bonding
 * Differences in charge will result in the live type being broken off
 * The charge can only be set in the same set the live was added, not down-set
 * only the union of members in the ns of the elements, types, and live types can set, there is no event handler to block this
 */

class LiveChargeIncrement extends Act\Pragma
{
    const UUID = '115a931b-0af7-467d-9ee7-6817d84b3317';
    const ACTION_NAME = TypeOfAction::PRAGMA_LIVE_CHARGE_INCREMENT;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Pragma::UUID,
        Act\CmdNoSideEffects::UUID
    ];


}

