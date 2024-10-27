<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Subtract extends BaseType
{
    const UUID = '18f455f3-39b0-4c84-92e1-21eb6af0236d';
    const TYPE_NAME = 'api_element_subtract';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\LiveTypeRemove::class,
    ];

}

