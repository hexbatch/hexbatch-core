<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;


#[HexbatchTitle( title: "Empties a set")]
#[HexbatchBlurb( blurb: "Will remove all non sticky elements without notice")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Empyting a set

Removes all non sticky elements without any events given.

Only the set admin can do this.
')]
class SetEmpty extends Act\Cmd\St
{
    const UUID = 'b1a1bc7c-a5b2-4cd1-9909-9355c8d38082';
    const ACTION_NAME = TypeOfAction::CMD_SET_EMPTY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];


}

