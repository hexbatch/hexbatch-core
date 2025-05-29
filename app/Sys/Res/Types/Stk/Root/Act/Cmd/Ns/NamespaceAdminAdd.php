<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

class NamespaceAdminAdd extends Act\Cmd\Ns
{
    const UUID = '14c0b718-0423-4fba-8d93-65a80eb184c5';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_ADMIN_ADD;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class
    ];

    const EVENT_CLASSES = [
        Evt\Element\NamespaceAdminAdding::class
    ];

}

