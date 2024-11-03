<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListElements extends Api\ElementApi
{
    const UUID = 'ec5c1437-ce47-4fcb-b8cf-88bb9dec9653';
    const TYPE_NAME = 'api_element_list';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class
    ];

}

