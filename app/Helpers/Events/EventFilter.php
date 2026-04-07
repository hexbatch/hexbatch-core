<?php

namespace App\Helpers\Events;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementLink;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\Server;
use App\Models\UserNamespace;

use Illuminate\Support\Collection;



class EventFilter
{

    public function __construct(
        public TypeOfEvent          $event_type,
        public ?ElementType         $type_context = null,
        public ?UserNamespace       $namespace_context = null,
        public ?Attribute           $attribute_context = null,
        public ?ElementSet          $set_context = null,
        public ?Element             $element_context = null,
        public ?Collection          $collection_context = null,
        public ?Server              $elsewhere_context = null,
        public ?Phase               $phase_context = null,
        public ?ElementLink         $link_context = null
    )
    {

    }



}

