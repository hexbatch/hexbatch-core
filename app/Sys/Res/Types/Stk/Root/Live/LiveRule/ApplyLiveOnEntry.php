<?php

namespace App\Sys\Res\Types\Stk\Root\Live\LiveRule;


use App\Data\ApiParams\Data\Live\LiveAppliedData;
use App\Enums\Types\TypeOfLiveRulePolicy;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Live\LiveRuleApplied;


class ApplyLiveOnEntry extends LiveRuleApplied
{
    const UUID = '4a6cd4ca-d83a-4f7d-a16c-196fb0e7cb61';

    const TypeOfLiveRulePolicy LIVE_RULE = TypeOfLiveRulePolicy::APPLY_LIVE_ON_ENTRY;


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

