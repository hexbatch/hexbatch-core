<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddElement extends Api\SetApi
{
    const UUID = 'be4df284-6dc0-4cba-b607-2cf6de540d87';
    const TYPE_NAME = 'api_set_add_element';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\St\SetMemberAdd::class,
    ];

}

