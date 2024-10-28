<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act\BaseAction;
use App\Sys\Res\Types\Stk\Root\Act\Cmd;


class St extends BaseAction
{
    const UUID = 'bc3c30aa-5a83-4038-bdef-32a913389983';
    const ACTION_NAME = TypeOfAction::BASE_SET;


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Cmd::class
    ];



}

