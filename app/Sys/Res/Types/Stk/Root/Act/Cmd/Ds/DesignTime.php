<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;

/**
 * Can be given another type to copy the schedule from
 */
class DesignTime extends Act\Cmd\Ds
{
    const UUID = '777c5080-dc81-40f8-8017-1a3a8a831a07';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_TIME;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

