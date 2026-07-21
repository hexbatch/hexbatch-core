<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class NamespaceLogin extends Evt\ScopeElement
{
    const UUID = 'c63b07f8-054d-4351-b4c0-a410acddceb4';
    const EVENT_NAME = TypeOfEvent::NAMESPACE_LOGIN;


    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

