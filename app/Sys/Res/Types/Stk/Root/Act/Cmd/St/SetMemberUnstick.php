<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


#[HexbatchTitle( title: "Makes one or more elements in a set unsticky")]
#[HexbatchBlurb( blurb: "UnSticks elements if not already. No events are raised")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Making one or more elements in a normal and not sticky.

The elements can be mixed types. If an element already not sticky, no error.

Only the set admin group can do this.

')]
class SetMemberUnstick extends Act\Cmd\St
{
    const UUID = '65055d51-c880-48b3-a5df-2c5316593c81';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_UNSTICK;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

}

