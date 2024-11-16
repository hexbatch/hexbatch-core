<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListSets extends Api\SetApi
{
    const UUID = 'bd6a0fef-b3bf-4f33-8988-6714ff385d71';
    const TYPE_NAME = 'api_set_sets';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Pa\Search::class
    ];

}

