<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class NamespaceMemberAdded extends Evt\ScopeElement implements ICommandCallable
{

    use Evt\Element\Traits\ElementNotificationEventTree;

    const UUID = '0c18863f-2807-482f-8e70-e8a5bde8e83a';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_MEMBER_ADDING;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

    /** @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Member added~ ');
    }

}

