<?php

namespace App\Sys\Res\Types\Stock\System\Event\Element;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Event;


class SearchResults extends BaseType
{
    const UUID = 'b084df58-12e3-48fa-bdd3-48f059a2dca9';
    const TYPE_NAME = 'event_search_results';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Event\ScopeElement::UUID
    ];

}

