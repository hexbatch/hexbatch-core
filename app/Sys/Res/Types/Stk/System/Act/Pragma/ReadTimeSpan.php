<?php

namespace App\Sys\Res\Types\Stk\System\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;


class ReadTimeSpan extends Act\Pragma
{
    const UUID = '7aca447b-968d-455c-8fc9-8c4705f89771';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ_TIME;
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

