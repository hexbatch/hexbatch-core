<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\NamespaceEditPromotionMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class NamespaceEditPromotion extends Act\Cmd\Ns
{
    const UUID = '8db598cc-a4b5-43db-966b-d015e1316bb8';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_EDIT_PROMOTION;

    const ATTRIBUTE_CLASSES = [
        NamespaceEditPromotionMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ns::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

