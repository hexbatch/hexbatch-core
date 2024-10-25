<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class Write extends Act\Pragma
{
    const UUID = '51e9a358-c2b1-4876-a518-0ab65d1be224';
    const ACTION_NAME = TypeOfAction::PRAGMA_WRITE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Pragma::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\AttributeWrite::class
    ];

}

