<?php

namespace App\Sys\Res\Types\Stk\Root\Live\LiveRule;


use App\Data\ApiParams\Data\Live\LiveAppliedData;
use App\Enums\Types\TypeOfLiveRulePolicy;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Live\LiveRuleApplied;


class BlockLiveOnEntry extends LiveRuleApplied
{
    const UUID = '0449edb4-0121-423b-8921-5c6f8c7e8e84';

    const TypeOfLiveRulePolicy LIVE_RULE = TypeOfLiveRulePolicy::BLOCKED_FROM_ENTRY;


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

