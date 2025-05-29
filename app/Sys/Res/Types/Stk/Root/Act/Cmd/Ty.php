<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Ty extends Cmd
{
    const UUID = '83fafab2-88dd-4652-a3a9-1fe39425270a';
    const ACTION_NAME = TypeOfAction::BASE_TYPE;




    const PARENT_CLASSES = [
        Cmd::class
    ];



}

