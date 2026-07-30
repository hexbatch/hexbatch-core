<?php

namespace App\Sys\Res\Types\Stk\Root\Live\LiveRule;


use App\Data\ApiParams\Data\Live\LiveAppliedData;
use App\Enums\Types\TypeOfLiveRulePolicy;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Live\LiveRuleApplied;


class DisableLiveOnExit extends LiveRuleApplied
{
    const UUID = 'b3260a7c-828c-4cd1-9425-10dd490096d1';

    const TypeOfLiveRulePolicy LIVE_RULE = TypeOfLiveRulePolicy::DISABLE_WHEN_LEAVING;


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

