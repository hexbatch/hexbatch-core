<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\Traits\NamespaceMembership;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICommandCallable;

class NamespaceMemberRemove extends Act\Cmd\Ns implements ICommandCallable
{
    use NamespaceMembership;
    const UUID = '6bf0c720-38f4-4387-8ef0-95780141846e';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_MEMBER_REMOVE;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class
    ];

    const EVENT_CLASSES = [
        Evt\Element\NamespaceMemberRemoved::class
    ];

    const POST_EVENT = self::EVENT_CLASSES[0];

    const IS_ADMIN = false;

    const IS_ADDING = false ;

}

