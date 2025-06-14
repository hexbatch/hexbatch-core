<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;

/**
 *   if no set provided, it will put new element in the caller's home set.
 */
class CreateElement extends Api\ElementApi
{
    const UUID = 'bad981d1-f817-4f89-879c-3d2d9c6443b6';
    const TYPE_NAME = 'api_types_create_element';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ty\ElementCreate::class,
    ];

}

