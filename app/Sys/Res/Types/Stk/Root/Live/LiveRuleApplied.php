<?php

namespace App\Sys\Res\Types\Stk\Root\Live;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\Live\LiveAppliedData;
use App\Enums\Types\TypeOfLiveRulePolicy;
use App\Models\Element;
use App\Models\ElementSet;
use App\Sys\Res\Types\Stk\Root\LiveRules;


#[HexbatchTitle( title: "Live rule base")]
#[HexbatchBlurb( blurb: "All live rule logic uses this as a base class")]
#[HexbatchDescription( description:'
 Any live applied to elements in a set are automatically popped off when the element leaves the set.
 Child sets can read the live applied by parent or ancestor set, but when setting their own, it will overwrite the live made by the earlier parent.

 Live actions only apply in set context, and not for the element in other sets that are not children or ancestors to where this is happening

    When rules followed, no event given when live added or removed, or if no permission given
')]
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

