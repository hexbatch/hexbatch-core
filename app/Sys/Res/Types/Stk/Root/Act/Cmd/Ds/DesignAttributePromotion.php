<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignAttributePromotionMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignAttributePromotion extends Act\Cmd
{
    const UUID = 'b5ee5ca7-0e73-404c-800f-365ec668501d';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_PROMOTION;


    const ATTRIBUTE_CLASSES = [
        DesignAttributePromotionMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

