<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Set;

use App\Enums\Sys\TypeOfEvent;
use App\Models\ActionDatum;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\Models\Server;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Evt;
use App\Sys\Res\Types\Stk\Root\Evt\BaseEvent;


class SetEnter extends Evt\ScopeSet
{
    const UUID = '946fb6f0-12bd-434b-8e34-e350bb38967a';
    const EVENT_NAME = TypeOfEvent::SET_ENTER;







    const PARENT_CLASSES = [
        Evt\ScopeSet::class
    ];

    public function getAskedAboutSet(): ?ElementSet
    {
        return $this->action_data?->data_set;
    }

    public function getAllowedElements(): array
    {
        return [];  //todo this is stubbed
    }



}

