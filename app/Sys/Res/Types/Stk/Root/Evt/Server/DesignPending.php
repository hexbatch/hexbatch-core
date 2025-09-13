<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Enums\Types\TypeOfApproval;
use App\Sys\Res\Types\Stk\Root\Evt;


class DesignPending extends Evt\ScopeServer
{
    const UUID = 'be5621ec-355d-48c4-a838-a3e0735fb3af';
    const EVENT_NAME = TypeOfEvent::DESIGN_PENDING;




    public function getApprovalStatus(): TypeOfApproval
    {
        return TypeOfApproval::PUBLISHING_APPROVED;  //todo this is stubbed
    }

    public function getAskedAboutAttributeName(): ?string
    {
        return $this->action_data->collection_data->offsetGet('attribute_name');
    }




    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

