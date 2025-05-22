<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


/**
 *
 */
class ElsewherePushCredentials extends Act\Cmd\Ew
{
    const UUID = '75ebb720-7ed4-4648-9d6e-09f2fe33198e';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_PUSH_CREDENTIALS;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ElsewhereCredentialsSending::class
    ];

}

