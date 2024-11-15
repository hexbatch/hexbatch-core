<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;

/**
 * Search results go to the path handle, if set.
 * This is going to be augmenting or refining the search or filtering
 */
class SearchResults extends Evt\ScopeElement
{
    const UUID = 'b084df58-12e3-48fa-bdd3-48f059a2dca9';
    const EVENT_NAME = TypeOfEvent::SEARCH_RESULTS;







    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

