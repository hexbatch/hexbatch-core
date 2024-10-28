<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\NamespaceAdminRemoveMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class NamespaceAdminRemove extends Act\Cmd
{
    const UUID = '4866a48c-c541-4f61-b38e-9c592f6da71b';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_ADMIN_REMOVE;

    const ATTRIBUTE_CLASSES = [
        NamespaceAdminRemoveMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
    ];

}

