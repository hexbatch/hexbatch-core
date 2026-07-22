<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;


use App\Enums\Sys\TypeOfEvent;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\UserNamespace;

class ScopeServer extends BaseEvent
{
    const UUID = '935a55bc-fbf9-4ffd-a6f5-63761e7c027e';
    const EVENT_NAME = TypeOfEvent::EVENT_SCOPE_SERVER;

    const ATTRIBUTE_CLASSES = [];
    const PARENT_CLASSES = [
        BaseEvent::class
    ];


    public function __construct(
        protected ?ElementType               $given_type = null  ,
        protected ?ElementType               $parent_type =null ,
        protected ?UserNamespace             $given_namespace = null,
        protected ?UserNamespace            $old_namespace = null,
        protected ?Element                  $given_element = null,
        protected ?ElementSet                  $given_set = null,
        protected ?Phase                    $given_phase = null ,
        protected ?Attribute            $given_attribute = null,

    )
    {

    }

}

