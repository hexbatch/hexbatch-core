<?php

namespace App\Sys\Res\Types\Stk\Root\Namespace;

use App\Sys\Res\Types\Stk\Root\NamespaceType;


/**
 * todo this is applied as a live when the user wants to delete their account, its added on the @uses \App\Sys\Res\Types\Stk\Root\Act\Cmd\Us\UserPrepareDeletion
 */
class DeletingUserMarker extends NamespaceType
{
    const UUID = '5ac098bd-7ee6-421e-bff8-849d20a60bfb';
    const TYPE_NAME = 'delete_user';





    const PARENT_CLASSES = [
        NamespaceType::class
    ];

}

