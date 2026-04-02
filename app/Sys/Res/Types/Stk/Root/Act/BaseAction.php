<?php

namespace App\Sys\Res\Types\Stk\Root\Act;

use App\Enums\Sys\TypeOfAction;
use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\UserNamespace;
use App\Sys\Res\Atr\Stk\Act\ActionMetric;
use App\Sys\Res\IAction;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Action;


class BaseAction extends BaseType
{
    const UUID = 'ebdcbddd-c746-44dc-84b0-cf1f8f174b2b';
    const ACTION_NAME = TypeOfAction::BASE_ACTION;


    public static function getHexbatchClassName() :string { return static::ACTION_NAME->value; }


    const ATTRIBUTE_CLASSES = [
        ActionMetric::class
    ];

    const PARENT_CLASSES = [
        Action::class
    ];

    const EVENT_CLASSES = [];


}

