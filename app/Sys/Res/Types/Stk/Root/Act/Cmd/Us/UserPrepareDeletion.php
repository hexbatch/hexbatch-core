<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Us;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * applies live on the user's private token:  @uses \App\Sys\Res\Types\Stk\Root\Namespace\DeletingUserMarker
 */
class UserPrepareDeletion extends Act\Cmd\Us
{
    const UUID = '0a221da7-3e9b-46b0-b181-a67a27aa4065';
    const ACTION_NAME = TypeOfAction::CMD_USER_PREPARE_DELETION;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Us::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\UserDeletionPreparing::class
    ];

}

