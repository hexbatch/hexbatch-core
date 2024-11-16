<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteElement extends Api\ElementApi
{
    const UUID = 'd1c5d047-dd64-401a-a80a-24bea97b0d72';
    const TYPE_NAME = 'api_types_promote_element';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ty\ElementPromote::class,
    ];

}

