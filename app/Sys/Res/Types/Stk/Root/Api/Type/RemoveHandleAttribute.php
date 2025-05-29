<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveHandleAttribute extends Api\TypeApi
{
    const UUID = '5ce720c5-9357-46eb-80b4-36f33bae50f8';
    const TYPE_NAME = 'api_type_attribute_remove_handle';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ty\AttributeHandleRemove::class
    ];

}

