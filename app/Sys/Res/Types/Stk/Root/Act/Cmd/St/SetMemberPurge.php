<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


#[HexbatchTitle( title: "Remove any elements from a set")]
#[HexbatchBlurb( blurb: "Will remove one or more  elements of different types")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Remove any element from a set

Removes one or more elements from a set, they can be of mixed types.

This includes sticky elements.

Only the set admin group can make the elements leave, and nothing can stop them.

The element and type owners will get an event after the fact

   * [SetLeave](../../../Evt/Set/SetLeave.php)


')]
class SetMemberPurge extends Act\Cmd\St
{
    const UUID = '92b452a7-e0a7-4449-af30-8220f68ab70e';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_PURGE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

}

