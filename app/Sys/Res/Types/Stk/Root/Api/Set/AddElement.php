<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddElement extends BaseType
{
    const UUID = 'be4df284-6dc0-4cba-b607-2cf6de540d87';
    const TYPE_NAME = 'api_set_add_element';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\St\SetMemberAdd::class,
    ];

}

