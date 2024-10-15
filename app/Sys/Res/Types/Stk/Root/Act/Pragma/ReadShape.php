<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;


class ReadShape extends Act\Pragma
{
    const UUID = 'f15a18a6-dbc2-4642-a481-26ed8ccdda72';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ_SHAPE;
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

