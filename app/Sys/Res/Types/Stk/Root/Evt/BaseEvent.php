<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;

use App\Enums\Sys\TypeOfEvent;
use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\Server;
use App\Models\UserNamespace;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\IEvent;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Event;


class BaseEvent extends Event implements IEvent
{
    const UUID = 'a8334729-4371-4fcc-ba73-39cfef6e2529';

    const EVENT_NAME = TypeOfEvent::BASE_EVENT;

    public static function getHexbatchClassName() :string { return static::EVENT_NAME->value; }
    public static function getEventName() :string { return static::EVENT_NAME->value; }



    const PARENT_CLASSES = [
        Event::class
    ];


    public function __construct(
        protected bool $b_type_init = false,
        protected ?bool           $is_async = null
    )
    {
        parent::__construct(b_type_init: $this->b_type_init,is_async: $this->is_async);
        Utilities::ignoreVar(static::EVENT_NAME);
    }

    /** @return static[] */
    public static function makeEventActions(BaseType $source,?ActionDatum $action_data = null,
                                     ?ElementType    $type_context = null,
                                     ?UserNamespace    $namespace_context = null,
                                            ?Attribute $attribute_context = null,
                                            ?ElementSet $set_context = null,
                                     ?Element        $element_context = null,
                                     ?Server         $elsewhere_context = null,
                                            ?Phase $phase_context = null,
                                            mixed $important_value = null

    ) : array
    {
        /** @var BaseEvent $system_type */
        $system_type = SystemTypes::getTypeByUuid(static::UUID);
        /**  todo use the @see \App\Models\ServerEvent to see if any waiting action chains, if so get them **/
        if (!$system_type) {throw new \InvalidArgumentException("cannot resolve event by uuid of ".static::UUID);}
        return  [];
        //todo sometimes when getting a tree, need to ask its own parents or nodes and get more from them (for all events)
    }

    public function getAskedAboutType(): ?ElementType
    {
        return $this->action_data?->data_type;
    }

    public function getParentType(): ?ElementType
    {
        return $this->action_data?->second_second_type;
    }


}

