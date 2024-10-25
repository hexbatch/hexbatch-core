<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class NamespaceMemberAdding extends Evt\ScopeElement
{
    const UUID = '0c18863f-2807-482f-8e70-e8a5bde8e83a';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_MEMBER_ADDING;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

