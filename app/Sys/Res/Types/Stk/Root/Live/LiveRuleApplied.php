<?php

namespace App\Sys\Res\Types\Stk\Root\Live;


use App\Data\ApiParams\Data\Live\LiveAppliedData;
use App\Enums\Types\TypeOfLiveRulePolicy;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\LiveRules;


class LiveRuleApplied extends LiveRules
{
    const UUID = '86514a09-584d-494f-af10-95f1a24bf0a0';

    const TypeOfLiveRulePolicy LIVE_RULE = TypeOfLiveRulePolicy::NO_RULE;
    const NAME_PREFIX = 'live_rule_';


    const PARENT_CLASSES = [
        LiveRules::class
    ];

    const ATTRIBUTE_CLASSES = [];


    public static function getTypeName(): string
    {
        return static::NAME_PREFIX . static::LIVE_RULE->value;
    }

    public static function doRule(ElementSet $set,Element $el)
    :?LiveAppliedData
    {
        return null;
    }


}

