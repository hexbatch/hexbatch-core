<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Type;

use App\Enums\Sys\TypeOfEvent;
use App\Enums\Types\TypeOfApproval;
use App\Models\ElementType;
use App\Sys\Res\Types\Stk\Root\Evt;


class ElementCreationBatch extends Evt\ScopeType
{
    const UUID = 'd995e77c-db66-4ab8-824e-3d511e5dea61';
    const EVENT_NAME = TypeOfEvent::ELEMENT_CREATION_BATCH;







    const PARENT_CLASSES = [
        Evt\ScopeType::class
    ];


    public function getAskedAboutType(): ?ElementType
    {
        return $this->action_data?->data_type;
    }

    public function getParentType(): ?ElementType
    {
        return $this->action_data?->second_second_type;
    }

    public function canCreate(): bool
    {
        return true;  //todo this is stubbed
    }

    public function getNumberAllowed(): ?int
    {
        return null;  //todo this is stubbed
    }

}

