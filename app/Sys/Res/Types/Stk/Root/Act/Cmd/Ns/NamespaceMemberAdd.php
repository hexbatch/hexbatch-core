<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class NamespaceMemberAdd extends Act\Cmd\Ns
{
    const UUID = 'da5fd4af-adf2-4920-b03d-72660fadc4d1';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_MEMBER_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class
    ];


    const EVENT_CLASSES = [
        Evt\Element\NamespaceMemberAdding::class
    ];

}

