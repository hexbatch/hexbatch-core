<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\DesignAttributeEditMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class DesignAttributeEdit extends Act\Cmd\Ds
{
    const UUID = 'b5dc244c-d966-48fd-9c42-ed53cceb827f';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_EDIT;

    const ATTRIBUTE_CLASSES = [
        DesignAttributeEditMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

