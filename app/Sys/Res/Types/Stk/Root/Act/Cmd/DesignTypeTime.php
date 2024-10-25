<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignTypeTime extends Act\Cmd
{
    const UUID = '777c5080-dc81-40f8-8017-1a3a8a831a07';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_TYPE_TIME;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

