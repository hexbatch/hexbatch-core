<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Link extends Api\ElementApi
{
    const UUID = 'af1e457d-7bc2-4467-8434-ae099a29123e';
    const TYPE_NAME = 'api_element_link';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ele\LinkAdd::class,
    ];

}

