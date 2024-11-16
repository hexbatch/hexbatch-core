<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class UnLink extends Api\ElementApi
{
    const UUID = '7dddcc46-b3dc-464a-8088-425c44c5b993';
    const TYPE_NAME = 'api_element_unlink';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ele\LinkRemove::class,
    ];

}

