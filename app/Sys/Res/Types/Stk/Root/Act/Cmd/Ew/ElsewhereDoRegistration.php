<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ew;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElsewhereDoRegistration extends Act\Cmd\Ew
{
    const UUID = 'ef63444b-45e6-4dea-a6c7-a9caee216ee2';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_DO_REGISTRATION;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ew::class,
        Act\SystemPrivilege::class,
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ServerRegistered::class
    ];

}

