<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteLive extends Api\ElementApi
{
    const UUID = 'aa71dfc6-0efd-4dc1-bfce-5f7c2d37f7c9';
    const TYPE_NAME = 'api_element_promote_live';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ele\LiveTypePromote::class,
    ];

}

