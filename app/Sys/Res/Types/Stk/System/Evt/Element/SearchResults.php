<?php

namespace App\Sys\Res\Types\Stk\System\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Evt;


class SearchResults extends Evt\ScopeElement
{
    const UUID = 'b084df58-12e3-48fa-bdd3-48f059a2dca9';
    const EVENT_NAME = TypeOfEvent::SEARCH_RESULTS;
    const TYPE_NAME = TypeOfEvent::SEARCH_RESULTS;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Evt\ScopeElement::UUID
    ];

}

