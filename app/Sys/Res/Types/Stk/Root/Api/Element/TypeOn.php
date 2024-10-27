<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class TypeOn extends BaseType
{
    const UUID = '1570126c-e9b8-4fca-a525-078a74ce5ab1';
    const TYPE_NAME = 'api_element_type_on';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Pragma\Search::class,
        Act\Pragma\TypeOn::class,
    ];

}

