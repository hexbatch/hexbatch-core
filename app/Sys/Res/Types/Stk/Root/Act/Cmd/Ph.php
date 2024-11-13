<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Ph extends Cmd
{
    const UUID = 'b601a70d-e4a3-4c13-a734-03b40980e118';
    const ACTION_NAME = TypeOfAction::BASE_PHASE;




    const PARENT_CLASSES = [
        Cmd::class
    ];



}

