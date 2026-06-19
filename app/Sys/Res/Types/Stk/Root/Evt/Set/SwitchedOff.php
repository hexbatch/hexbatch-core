<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;


class SwitchedOff extends Evt\ScopeSet
{
    use Evt\Element\Traits\ElementNotificationEventTree;

    const UUID = '3672f617-ba23-4c71-b90b-5f56a78afd25';
    const EVENT_NAME = TypeOfEvent::SWITCHED_OFF;


    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

    public function __construct(
        protected Element             $given_element,
        protected ?ElementSet             $given_set
    )
    {

    }



    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'Turned off');
    }

}

