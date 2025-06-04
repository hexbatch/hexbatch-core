<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\St;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


#[HexbatchTitle( title: "Destroys a set")]
#[HexbatchBlurb( blurb: "Can destory a top level or child set")]
#[HexbatchDescription( description: /** @lang markdown */
    '
# Destroys a set

Destroys a set. If there are any child sets, or nested child sets, those are destroyed first

Unless there is a handler for the event below, only the set admin group can destory it.
When a set contains child sets, all the event handlers have to agree for each set to be destroyed first,
 or the destroyer is in the admin group for all the sets.

 This includes nested sets of any level. If any permission or event fails, then no set is destroyed and this fails.

Set owners and type owners will get the event

* [SetDestroying](../../../Evt/Server/SetDestroying.php)

Only one event response is needed to stop the destruction

Once destroyed, if this is a child set, and the parent remains, the parent sets above this child set will get the event to notify them

   * [SetChildDestroyed](../../../Evt/Set/SetChildDestroyed.php)


')]
class SetDestroy extends Act\Cmd\St
{
    const UUID = 'bb92f8d7-1bdf-4dec-9ba6-d903bfc075c2';
    const ACTION_NAME = TypeOfAction::CMD_SET_DESTROY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\St::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\SetDestroyed::class,
        Evt\Server\SetDestroying::class,
        Evt\Set\SetChildDestroyed::class
    ];

}

