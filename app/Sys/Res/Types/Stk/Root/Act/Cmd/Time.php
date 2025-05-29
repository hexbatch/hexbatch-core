<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Time extends Cmd
{
    const UUID = '7af5f5bd-3581-415f-8178-bdae165f0042';
    const ACTION_NAME = TypeOfAction::BASE_TIME;




    const PARENT_CLASSES = [
        Cmd::class
    ];



}

