<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 *
 */
class ElsewhereNewCredentials extends Act\Cmd
{
    const UUID = '53ec6380-8528-4daf-8375-51858083299e';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_NEW_CREDENTIALS;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

