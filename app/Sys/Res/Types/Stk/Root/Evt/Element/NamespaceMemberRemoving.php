<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class NamespaceMemberRemoving extends Evt\ScopeElement
{
    const UUID = '3a7a2ad4-855d-42bf-aa36-654c0c30bf32';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_MEMBER_REMOVING;







    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

