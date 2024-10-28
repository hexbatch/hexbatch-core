<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Edit extends BaseType
{
    const UUID = '06ff0762-72bb-4130-bb9a-fc89707b95a9';
    const TYPE_NAME = 'api_design_edit';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignEdit::class,
    ];

}

