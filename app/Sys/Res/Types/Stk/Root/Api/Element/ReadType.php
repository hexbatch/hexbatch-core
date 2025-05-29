<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ReadType extends Api\ElementApi
{
    const UUID = '818c59fe-69f5-43bf-9cfe-214082dabcee';
    const TYPE_NAME = 'api_element_read_type';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Pa\Search::class,
    ];

}

