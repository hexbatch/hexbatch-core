<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddHandle extends BaseType
{
    const UUID = 'e6f0544f-bae2-4d3a-82d1-ef169097368a';
    const TYPE_NAME = 'api_set_add_handle';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\SetChildHandleAdd::class,
    ];

}

