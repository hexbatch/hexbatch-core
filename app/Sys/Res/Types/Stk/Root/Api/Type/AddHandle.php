<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddHandle extends BaseType
{
    const UUID = '015c71a6-4978-4dd0-bb31-15e2447414b2';
    const TYPE_NAME = 'api_type_handle_add';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\TypeHandleAdd::class
    ];

}

