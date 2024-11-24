<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Us;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * //todo  deletes the namespaces, including the default
 * checks the @uses \App\Sys\Res\Types\Stk\Root\Namespace\DeletingUserMarker
 */
class UserStartDeletion extends Act\Cmd\Ns
{
    const UUID = 'fe677c59-7ebe-4a0d-ba3e-4cba4ef13c08';
    const ACTION_NAME = TypeOfAction::CMD_USER_START_DELETION;

    const ATTRIBUTE_CLASSES = [
        Metrics\UserStartDeletionMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Us::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\UserDeletionStarting::class
    ];

}

