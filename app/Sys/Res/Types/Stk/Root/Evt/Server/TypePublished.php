<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Enums\Types\TypeOfApproval;
use App\Models\Attribute;
use App\Sys\Res\Types\Stk\Root\Evt;


class TypePublished extends Evt\ScopeServer
{
    const UUID = 'f470d540-308c-4d88-8204-88a077480581';
    const EVENT_NAME = TypeOfEvent::TYPE_PUBLISHED;







    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];






    public function getParentAttribute(): ?Attribute
    {
        return $this->action_data?->data_attribute;
    }

    public function getApprovalStatus(): TypeOfApproval
    {
        return TypeOfApproval::PUBLISHING_APPROVED;  //todo this is stubbed
    }

}

