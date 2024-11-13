<?php

namespace App\Sys\Res\Types\Stk\Root\Act;


use App\Sys\Res\Types\BaseType;

/**
 * todo this cannot be inherited without permission!
 *
 */
class SystemPrivilege extends BaseType
{
    const UUID = '19e3763f-9afa-4094-b6bb-67f26af2f1b7';
    const TYPE_NAME = 'system_privilege';





    const PARENT_CLASSES = [
        BaseAction::class
    ];

}

