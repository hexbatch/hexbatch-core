<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\Set\Traits\SetNotificationEventTree;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class TypeMapEnclosedStart extends Evt\ScopeSet implements ICommandCallable
{
    use SetNotificationEventTree;
    const UUID = '7f7591b5-65d4-4230-98b5-bf6253bc768a';
    const EVENT_NAME = TypeOfEvent::TYPE_MAP_ENCLOSED_START;

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
        return static::doCallInner($command_args,$children_args,'Map enclosed start');
    }

}

