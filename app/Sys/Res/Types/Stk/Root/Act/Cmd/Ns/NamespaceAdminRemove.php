<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\Traits\NamespaceMembership;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICommandCallable;

class NamespaceAdminRemove extends Act\Cmd\Ns implements ICommandCallable
{
    use NamespaceMembership;
    const UUID = '4866a48c-c541-4f61-b38e-9c592f6da71b';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_ADMIN_REMOVE;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

    const EVENT_CLASSES = [
        Evt\Element\NamespaceAdminRemoved::class
    ];

    const POST_EVENT = self::EVENT_CLASSES[0];

    const IS_ADMIN = true;

    const IS_ADDING = false;

}

