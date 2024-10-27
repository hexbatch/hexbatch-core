<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class EditListenerRule extends BaseType
{
    const UUID = 'fec21736-d01c-4dc1-9480-7ddcac2bc58f';
    const TYPE_NAME = 'api_design_edit_listener_rule';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\DesignRuleEdit::class,
    ];

}

