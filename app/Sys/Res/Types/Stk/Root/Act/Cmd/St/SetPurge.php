<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


#[HexbatchTitle( title: "Empties a set")]
#[HexbatchBlurb( blurb: "Will remove all elements without notice")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Purging a set

Removes all elements without any events given.

Non-sticky will be removed too.

Only the set admin can do this.
')]
class SetPurge extends Act\Cmd\St
{
    const UUID = 'd0d23dc0-d588-4a51-b10b-b2f3a8cfd49a';
    const ACTION_NAME = TypeOfAction::CMD_SET_PURGE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class,
        Act\SystemPrivilege::class,
        Act\NoEventsTriggered::class,
    ];

}

