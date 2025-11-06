<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class NamespaceMemberRemoved extends Evt\ScopeElement implements ICommandCallable
{
    use Evt\Element\Traits\ElementNotificationEventTree;

    const UUID = '3a7a2ad4-855d-42bf-aa36-654c0c30bf32';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_MEMBER_REMOVING;

    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

    /** @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Member removed~ ');
    }

}

