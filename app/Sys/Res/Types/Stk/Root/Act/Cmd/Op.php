<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\BaseAction;


class Op extends BaseAction
{
    const UUID = 'ae7a8d52-f1f9-4740-9db5-0df3e5819cd4';
    const ACTION_NAME = TypeOfAction::BASE_OPERATION;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        BaseAction::class
    ];



}

