<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Set\Traits\SetNotificationEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class TypeMapEnclosedEnd extends Evt\ScopeSet implements ICommandCallable
{
    use SetNotificationEventTree;
    const UUID = '67cf7008-790b-44cd-ac7f-b4a9a094cff1';
    const EVENT_NAME = TypeOfEvent::TYPE_MAP_ENCLOSED_END;


    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

    public function __construct(
        protected ElementSet            $given_set,
        protected Element             $given_element

    )
    {

    }


    /**  @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Map enclosed end');
    }

}

