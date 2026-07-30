<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class NamespaceAdminAdded extends Evt\ScopeElement implements ICommandCallable
{
    use Evt\Element\Traits\ElementNotificationEventTree;

    const UUID = '00e105a0-5b7f-4a8c-b80f-84f8f83b56ba';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_ADMIN_ADDING;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];


    /** @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Admin added~ ');
    }

}

