<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class NamespaceAdminRemoved extends Evt\ScopeElement implements ICommandCallable
{
    use Evt\Element\Traits\ElementNotificationEventTree;

    const UUID = 'e342570b-7241-4af7-9d38-196fb2ff1363';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_ADMIN_REMOVING;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];


    /** @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Admin removed~ ');
    }

}

