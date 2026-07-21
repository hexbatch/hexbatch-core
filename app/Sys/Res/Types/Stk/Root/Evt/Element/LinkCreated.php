<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;


class LinkCreated extends Evt\ScopeElement implements ICommandCallable
{
    use Evt\Element\Traits\ElementNotificationEventTree;

    const UUID = 'b1c70fce-690b-418f-827d-982f6d84e256';
    const EVENT_NAME = TypeOfEvent::LINK_CREATED;



    public function __construct(
        protected Element             $given_element,
        protected ?ElementSet             $given_set
    )
    {

    }



    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

    /** @throws \Throwable */
    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        return static::doCallInner($command_args,$children_args,'link created~ ');
    }

}

