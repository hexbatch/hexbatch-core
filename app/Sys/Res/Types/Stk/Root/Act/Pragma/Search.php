<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/*
 * Can either do search on a path,
 *  or a collection of elements that are path handles
 *    the collection will return multiple search results with logic
 *
 */
class Search extends Act\Pragma
{
    const UUID = '5b8ff68a-8748-4323-8088-04a8e6fa73fc';
    const ACTION_NAME = TypeOfAction::PRAGMA_SEARCH;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Pragma::UUID
    ];

    const EVENT_UUIDS = [
        Evt\Element\SearchResults::UUID,
    ];

}

