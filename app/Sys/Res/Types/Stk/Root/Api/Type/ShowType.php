<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Enums\Types\TypeOfLifecycle;
use App\Sys\Res\Types\Stk\Root\Api;


class ShowType extends Api\Design\ShowDesign
{
    const UUID = '43468b50-f2c9-468b-ae37-9dcf02332ea7';
    const TYPE_NAME = 'api_type_show';


    const PARENT_CLASSES = [
        Api\TypeApi::class
    ];

    const FILTER_OF_LIFECYCLE = TypeOfLifecycle::PUBLISHED;

}

