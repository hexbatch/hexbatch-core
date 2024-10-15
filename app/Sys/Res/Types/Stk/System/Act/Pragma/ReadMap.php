<?php

namespace App\Sys\Res\Types\Stk\System\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;


class ReadMap extends Act\Pragma
{
    const UUID = 'a347cb93-e33d-40bf-bc94-e0a191c1188c';
    const ACTION_NAME = TypeOfAction::PRAGMA_READ_MAP;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Pragma::UUID
    ];

}

