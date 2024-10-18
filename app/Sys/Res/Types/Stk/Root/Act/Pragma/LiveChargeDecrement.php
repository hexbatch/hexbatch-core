<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 * reduces the charge by 1
 * see increment
 */
class LiveChargeDecrement extends Act\Pragma
{
    const UUID = '802f6c4a-cc7c-4268-828d-419ba7f73fce';
    const ACTION_NAME = TypeOfAction::PRAGMA_LIVE_CHARGE_DECREMENT;
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

