<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * types which share the same handle are published and have other lifecycle changes, done  in a group,
 * if any type fails to change, then none do
 */
class TypeHandleAdd extends Act\Cmd\Ty
{
    const UUID = 'c79c2c70-f92e-4fdc-9cff-db756f8a1c8b';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_HANDLE_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];


    const EVENT_CLASSES = [
        Evt\Server\TypeHandleAdded::class
    ];

}

