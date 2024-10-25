<?php

namespace App\Sys\Res\Types\Stk\Root\Evt\Element;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\Types\Stk\Root\Evt;


class SearchResults extends Evt\ScopeElement
{
    const UUID = 'b084df58-12e3-48fa-bdd3-48f059a2dca9';
    const EVENT_NAME = TypeOfEvent::SEARCH_RESULTS;





    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Evt\ScopeElement::class
    ];

}

