<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ElsewhereGiveCredentialsMetric;
use App\Sys\Res\Types\Stk\Root\Act;

/**
 *
 */
class ElsewhereGiveCredentials extends Act\Cmd
{
    const UUID = '53ec6380-8528-4daf-8375-51858083299e';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_GIVE_CREDENTIALS;

    const ATTRIBUTE_CLASSES = [
        ElsewhereGiveCredentialsMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

}

