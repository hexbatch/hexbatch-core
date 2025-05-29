<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;



#[HexbatchTitle( title: "Makes one or more elements in a set sticky")]
#[HexbatchBlurb( blurb: "Sticks elements if not already. No events are raised")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Making one or more elements in a set sticky

The elements can be mixed types. If an element already sticky, no error.

Only the set admin group can do this.

')]
class SetMemberStick extends Act\Cmd\St
{
    const UUID = '3f6b9034-ecdf-4c13-af07-605cd1d8cca2';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_STICK;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

}

