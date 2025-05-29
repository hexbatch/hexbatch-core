<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Server extends Cmd
{
    const UUID = 'ebf25e30-835a-491b-9235-48bb743e5634';
    const ACTION_NAME = TypeOfAction::BASE_SERVER;




    const PARENT_CLASSES = [
        Cmd::class
    ];



}

