<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class Us extends Cmd
{
    const UUID = '6f421997-1182-48ae-b526-a89e9a274fb7';
    const ACTION_NAME = TypeOfAction::BASE_USER;




    const PARENT_CLASSES = [
        Cmd::class
    ];



}

