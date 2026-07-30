<?php

namespace App\Sys\Res\Types\Stk\Root\Live\LiveRule;


use App\Data\ApiParams\Data\Live\LiveAppliedData;
use App\Enums\Types\TypeOfLiveRulePolicy;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Live\LiveRuleApplied;


class DisableLiveOnEntry extends LiveRuleApplied
{
    const UUID = '76d412fa-a9a5-4299-b8eb-7dcd4de93ac3';

    const TypeOfLiveRulePolicy LIVE_RULE = TypeOfLiveRulePolicy::DISABLE_LIVE_ON_ENTRY;


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

