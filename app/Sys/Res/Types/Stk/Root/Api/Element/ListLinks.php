<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListLinks extends Api\ElementApi
{
    const UUID = '332c147f-bc67-4576-b168-8a44c60b98f6';
    const TYPE_NAME = 'api_element_list_links';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class
    ];

}

