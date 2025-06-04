<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

#[HexbatchTitle( title: "Remove non-sticky elements from a set")]
#[HexbatchBlurb( blurb: "Will remove one or more elements of different types")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Remove non-sticky elements from a set

Removes one or more elements from a set, they can be of mixed types.

Sticky elements will not be removed, those must be purged

Only the set admin group can make the elements leave, and nothing can stop them.

The element and type owners will get an event after the fact

   * [SetLeave](../../../Evt/Set/SetLeave.php)


')]
class SetMemberRemove extends Act\Cmd\St
{
    const UUID = '3cf263d1-3aef-4c96-aed4-01a3c2bd1f98';
    const ACTION_NAME = TypeOfAction::CMD_SET_MEMBER_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\SetLeave::class,
        Evt\Set\ShapeLeave::class,
        Evt\Set\MapLeave::class,
        Evt\Set\TypeMapEnclosedEnd::class,
        Evt\Set\TypeMapEnclosingEnd::class,
        Evt\Set\TypeShapeEnclosedEnd::class,
        Evt\Set\TypeShapeEnclosingEnd::class,
    ];

}

