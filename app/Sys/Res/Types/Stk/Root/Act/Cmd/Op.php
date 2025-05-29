<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\BaseAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Op extends Cmd
{
    const UUID = 'ae7a8d52-f1f9-4740-9db5-0df3e5819cd4';
    const ACTION_NAME = TypeOfAction::BASE_OPERATION;




    const PARENT_CLASSES = [
        BaseAction::class
    ];



}

