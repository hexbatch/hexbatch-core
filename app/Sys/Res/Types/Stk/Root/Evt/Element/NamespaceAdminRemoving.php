<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class NamespaceAdminRemoving extends Evt\ScopeElement
{
    const UUID = 'e342570b-7241-4af7-9d38-196fb2ff1363';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_ADMIN_REMOVING;







    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

