<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Read extends BaseType
{
    const UUID = 'ae6b7b0e-8991-4443-9f00-3e9a637a52ce';
    const TYPE_NAME = 'api_element_read';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Pragma\Search::class,
        Act\Pragma\Read::class,
    ];

}

