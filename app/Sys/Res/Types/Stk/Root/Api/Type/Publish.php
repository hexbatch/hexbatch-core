<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Annotations\ApiParamMarker;
use App\OpenApi\Params\Type\TypeParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;

#[ApiParamMarker( param_class: TypeParams::class)]
class Publish extends Api\TypeApi
{
    const UUID = '81c04881-39a5-4903-aaf2-34633b6f4f69';
    const TYPE_NAME = 'api_type_publish';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ty\TypePublish::class
    ];

}

