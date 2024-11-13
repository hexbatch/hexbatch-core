<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Wa extends Cmd
{
    const UUID = '5b08560a-2c2f-43dc-bac8-382895baaed1';
    const ACTION_NAME = TypeOfAction::BASE_WAIT;




    const PARENT_CLASSES = [
        Cmd::class
    ];



}

