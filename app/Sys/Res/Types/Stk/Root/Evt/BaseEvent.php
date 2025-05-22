<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;

use App\Enums\Sys\TypeOfEvent;
use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\Server;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\IEvent;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Event;
use BlueM\Tree;


class BaseEvent extends Event implements IEvent
{
    const UUID = 'a8334729-4371-4fcc-ba73-39cfef6e2529';

    const EVENT_NAME = TypeOfEvent::BASE_EVENT;

    public static function getClassName() :string { return static::EVENT_NAME->value; }
    public static function getEventName() :string { return static::EVENT_NAME->value; }



    const PARENT_CLASSES = [
        Event::class
    ];


    public function __construct(
        protected bool $b_type_init = false
    )
    {
        parent::__construct(b_type_init: $this->b_type_init);
    }

    /** @return static[] */
    public static function makeEventActions(BaseType $source,?ActionDatum $data = null,
                                     ?ElementType $type_context = null,?ElementSet $set_context = null,
                                     ?Element $element_context = null,
                                     ?Server $elsewhere_context = null,?Phase $phase_context = null

    ) : array
    {
        /** @var BaseEvent $system_type */
        $system_type = SystemTypes::getTypeByUuid(static::UUID);
        /**  todo use the @see \App\Models\ServerEvent to see if any waiting action chains, if so get them **/
        if (!$system_type) {throw new \InvalidArgumentException("cannot resolve event by uuid of ".static::UUID);}
        return  [];
    }


}

