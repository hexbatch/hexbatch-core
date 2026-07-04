<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;

/**
 * there was a phase edit action and the type was added into one or more places
 */
class PhaseChangedQuiet extends Evt\ScopeType
{
    use Evt\Element\Traits\ElementNotificationEventTree;

    const UUID = '5f0ac67d-97c7-4443-84b5-9c3fbf9d387c';
    const EVENT_NAME = TypeOfEvent::ELEMENT_PHASE_CHANGED_QUIET;


    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];

    /**
     * @throws \Throwable
     */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'phase changed quietly~ ');
    }

}

