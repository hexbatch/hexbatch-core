<?php

namespace App\Sys\Res\Types\Stk\Root\Live\LiveRule;


use App\Data\ApiParams\Data\Live\LiveAppliedData;
use App\Enums\Types\TypeOfLiveRulePolicy;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\Live\LiveRuleApplied;


class EnableLiveOnEntry extends LiveRuleApplied
{
    const UUID = '4ad11f0d-a10e-41f0-9fac-ecaafef8a53a';

    const TypeOfLiveRulePolicy LIVE_RULE = TypeOfLiveRulePolicy::ENABLE_LIVE_ON_ENTRY;


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

