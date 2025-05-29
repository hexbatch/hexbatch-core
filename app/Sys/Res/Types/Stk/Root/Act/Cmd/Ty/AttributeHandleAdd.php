<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class AttributeHandleAdd extends Act\Cmd\Ty
{
    const UUID = '8a3fbc96-6772-493e-937d-e0306fa46fbc';
    const ACTION_NAME = TypeOfAction::CMD_ATTRIBUTE_HANDLE_ADD;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\AttributeHandleAdded::class
    ];

}

