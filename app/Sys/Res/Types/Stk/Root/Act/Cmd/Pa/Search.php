<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa;

use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/*
 * Can either do search on a path,
 *  or a collection of elements that are path handles
 *    the collection will return multiple search results with logic
 *
 * empty results, or 0 for integer returns, or not exist : is false to the parent, otherwise true for parent logic
 *
 */
class Search extends Act\Cmd\Ele
{
    const UUID = '5b8ff68a-8748-4323-8088-04a8e6fa73fc';
    const ACTION_NAME = TypeOfAction::PRAGMA_SEARCH;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\Pragma::class,
        Act\CmdNoSideEffects::class
    ];

    const EVENT_CLASSES = [
        Evt\Element\SearchResults::class,
    ];

}

