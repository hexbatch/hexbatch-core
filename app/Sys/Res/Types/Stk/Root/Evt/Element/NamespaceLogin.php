<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class NamespaceLogin extends Evt\ScopeElement
{
    const UUID = '62ce592d-ce7c-4efc-aa42-958e4bc8cca4';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_LOGIN;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

