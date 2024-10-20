<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Models\ElementType;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;


/**
 * if a ns is member in the element type's @see ElementType::$energy_namespace_id,ElementType::$owner_namespace_id
 * then can adjust the energy in the element by any amount
 * then energy reduced by -1 for each live added to the element
 *            no change to energy when live removed
 */
class WriteEnergy extends Act\Pragma
{
    const UUID = 'a8adb744-7b45-4b9f-90f8-d32115b850e7';
    const ACTION_NAME = TypeOfAction::PRAGMA_WRITE_ENERGY;
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

