<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class EditAttribute extends BaseType
{
    const UUID = '40a60d68-5fb3-472d-9c90-bc033501ab1b';
    const TYPE_NAME = 'api_design_edit_attribute';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignAttributeEdit::class,
    ];

}

