<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class PrepareUserDeletion extends Act\Cmd\Ns
{
    const UUID = '0a221da7-3e9b-46b0-b181-a67a27aa4065';
    const ACTION_NAME = TypeOfAction::CMD_PREPARE_USER_DELETION;

    const ATTRIBUTE_CLASSES = [
        Metrics\PrepareUserDeletionMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

    const EVENT_CLASSES = [
        Evt\Server\UserDeletionStarting::class
    ];

}

