<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Server;

use App\Enums\Sys\TypeOfEvent;
use App\Enums\Types\TypeOfApproval;
use App\Models\ElementType;
use App\Sys\Res\Types\Stk\Root\Evt;


class DesignPending extends Evt\ScopeServer
{
    const UUID = 'be5621ec-355d-48c4-a838-a3e0735fb3af';
    const EVENT_NAME = TypeOfEvent::DESIGN_PENDING;


    public function getAskedAboutType(): ?ElementType
    {
        return $this->action_data?->data_type;
    }

    public function getParentType(): ?ElementType
    {
        return $this->action_data?->second_second_type;
    }

    public function getApprovalStatus(): TypeOfApproval
    {
        return TypeOfApproval::PUBLISHING_APPROVED;  //todo this is stubbed
    }




    const PARENT_CLASSES = [
        Evt\ScopeServer::class
    ];

}

