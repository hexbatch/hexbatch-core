<?php

namespace App\Sys\Res\Types\Stk\Root\Act;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Op extends BaseAction
{
    const UUID = 'ae7a8d52-f1f9-4740-9db5-0df3e5819cd4';
    const TYPE_NAME = 'action_operation';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        BaseAction::UUID
    ];



}

