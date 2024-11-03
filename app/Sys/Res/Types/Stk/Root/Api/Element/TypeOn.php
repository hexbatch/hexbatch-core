<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class TypeOn extends Api\ElementApi
{
    const UUID = '1570126c-e9b8-4fca-a525-078a74ce5ab1';
    const TYPE_NAME = 'api_element_type_on';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\TypeOn::class,
    ];

}

