<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 * asking elsewhere for new credentials
 */
class ElsewhereAskCredentials extends Act\Cmd
{
    const UUID = '424d4d74-14f2-4b39-ba7f-8520bfc25852';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_ASK_CREDENTIALS;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

