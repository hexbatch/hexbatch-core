<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * @see MasterSemaphore
 * Runs a published master
 *
 */
class SemaphoreMasterRun extends Act\Cmd
{
    const UUID = 'd5895d42-9383-4d4d-9e45-ce7d5c0c5580';
    const ACTION_NAME = TypeOfAction::CMD_SEMAPHORE_MASTER_RUN;
    const TYPE_NAME = self::ACTION_NAME;

    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

