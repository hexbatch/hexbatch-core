<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Purge extends BaseType
{
    const UUID = '48935711-8f68-457d-a746-d20deb8505bf';
    const TYPE_NAME = 'api_set_purge';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\SetPurge::class,
    ];

}

