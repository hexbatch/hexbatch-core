<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListElsewhere extends Api\ElsewhereApi
{
    const UUID = '8f77d0ef-9ae9-4cbb-a071-d9dda2042c18';
    const TYPE_NAME = 'api_elsewhere_list';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Pa\Search::class
    ];

}

