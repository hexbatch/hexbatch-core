<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


#[HexbatchTitle( title: "Remove a link")]
#[HexbatchBlurb( blurb: "Can unlink a set from an element")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Unlinking sets

Removes a set from an element linking to it.

Once linked, the set can be  unlinked, if no event handler for the element,
then only permission check is that the calling namespace is in element admin group

The element and type owners will recieve a

   * [LinkDestroying](../../../Evt/Server/LinkDestroying.php)

If all report back ok, then the link is undone.

Once the link is removed, the element and type owners will get an event
   * [LinkDestroyed](../../../Evt/Server/LinkDestroyed.php)


')]
class LinkRemove extends LinkAdd
{
    const UUID = 'c0f2f5b9-3030-4e60-9bd0-742299a6b83b';
    const ACTION_NAME = TypeOfAction::CMD_LINK_REMOVE;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\LinkDestroyed::class,
        Evt\Server\LinkDestroying::class
    ];

    const PRE_EVENT_CLASS = Evt\Server\LinkDestroying::class;

    const POST_EVENT_CLASS = Evt\Server\LinkDestroyed::class;

    const bool IS_ADDING = false;

}

