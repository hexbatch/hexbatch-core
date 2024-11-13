<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteEdit extends Api\ElementApi
{
    const UUID = '6cc81b74-fe48-4af0-9c07-1cb5b5378c56';
    const TYPE_NAME = 'api_element_promote_edit';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\ElementPromoteEdit::class,
    ];

}

