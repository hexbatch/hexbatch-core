<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Ds extends Cmd
{
    const UUID = 'f8702a5b-9dee-4a9e-9db9-ea93142dfa7b';
    const ACTION_NAME = TypeOfAction::BASE_DESIGN;




    const PARENT_CLASSES = [
        Cmd::class
    ];



}

