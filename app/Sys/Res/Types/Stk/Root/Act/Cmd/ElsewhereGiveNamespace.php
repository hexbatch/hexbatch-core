<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Atr\Stk\Act\Metrics\ElsewhereGiveNamespaceMetric;
use App\Sys\Res\Types\Stk\Root\Act;


class ElsewhereGiveNamespace extends Act\Cmd
{
    const UUID = '3fa65eaf-79c0-4097-89aa-84d4c4643215';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_GIVE_NS;

    const ATTRIBUTE_CLASSES = [
        ElsewhereGiveNamespaceMetric::class
    ];

    const PARENT_CLASSES = [
        Act\Cmd::class
    ];

}

