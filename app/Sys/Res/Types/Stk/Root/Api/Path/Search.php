<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Search extends BaseType
{
    const UUID = '91b6d6a5-347b-4090-b87c-19b8395a84da';
    const TYPE_NAME = 'api_path_search';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Pragma\Search::class,
    ];

}

