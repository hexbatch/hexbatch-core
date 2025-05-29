<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListPublished extends Api\TypeApi
{
    const UUID = '9439a2ea-427a-468f-9bdf-9a5fb58157b6';
    const TYPE_NAME = 'api_type_list_published';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Pa\Search::class
    ];

}

