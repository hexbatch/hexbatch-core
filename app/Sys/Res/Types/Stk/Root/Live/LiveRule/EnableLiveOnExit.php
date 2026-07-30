<?php

namespace App\Sys\Res\Types\Stk\Root\Live\LiveRule;


use App\Data\ApiParams\Data\Live\LiveAppliedData;
use App\Enums\Types\TypeOfLiveRulePolicy;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Live\LiveRuleApplied;


class EnableLiveOnExit extends LiveRuleApplied
{
    const UUID = 'b04239fc-8fee-4b3b-a4e7-4472c13c82a6';

    const TypeOfLiveRulePolicy LIVE_RULE = TypeOfLiveRulePolicy::ENABLE_WHEN_LEAVING;


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

