<?php

namespace App\Sys\Res\Types\Stk\Root\Live\LiveRule;


use App\Data\ApiParams\Data\Live\LiveAppliedData;
use App\Enums\Types\TypeOfLiveRulePolicy;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Live\LiveRuleApplied;


class DropLiveOnExit extends LiveRuleApplied
{
    const UUID = 'f19a5d7e-f0d4-4992-8586-cda6bf2ea7f1';

    const TypeOfLiveRulePolicy LIVE_RULE = TypeOfLiveRulePolicy::DROP_WHEN_LEAVING;


    const PARENT_CLASSES = [
        LiveRuleApplied::class
    ];

    const ATTRIBUTE_CLASSES = [];


    public static function doRule(ElementSet $set,Element $el)
    :?LiveAppliedData
    {
        return null;
    }

}

