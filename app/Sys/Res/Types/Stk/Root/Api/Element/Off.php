<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Off extends BaseType
{
    const UUID = '227d48f8-190a-4155-8695-bce17780837b';
    const TYPE_NAME = 'api_element_off';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Pragma\Search::class,
        Act\Pragma\ElementOff::class,
    ];

}

