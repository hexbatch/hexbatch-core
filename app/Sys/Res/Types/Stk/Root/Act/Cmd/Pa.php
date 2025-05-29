<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Pa extends Cmd
{
    const UUID = 'be3c9b0d-dc6b-40d0-b227-87f7b859bc7b';
    const ACTION_NAME = TypeOfAction::BASE_PATH;




    const PARENT_CLASSES = [
        Cmd::class
    ];



}

