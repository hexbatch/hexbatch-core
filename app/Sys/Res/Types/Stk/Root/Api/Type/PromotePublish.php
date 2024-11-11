<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromotePublish extends Api\DesignApi
{
    const UUID = 'b9f16171-d5b7-4b61-9d9a-14523753eca2';
    const TYPE_NAME = 'api_design_promote_publish';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ty\TypePublishPromote::class,
    ];

}

