<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * @see MasterSemaphore for the setup,
 * it takes a type made for this, that is published,
 * and makes the new types for the master group, but they are in developer mode
 * need to publish them all @uses TypePublish
 *
 */
class SemaphoreMasterCreate extends Act\Cmd
{
    const UUID = 'e6bf1d5c-0bf3-440c-8e29-9f18cee4d409';
    const ACTION_NAME = TypeOfAction::CMD_SEMAPHORE_MASTER_CREATE;
    const TYPE_NAME = self::ACTION_NAME;

    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

