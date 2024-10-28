<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ChangeOwner extends BaseType
{
    const UUID = '513a16a3-cbb5-4f6e-a6e4-4e7b90b0a1c6';
    const TYPE_NAME = 'api_element_change_owner';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\ElementChangeOwner::class,
    ];

}

