<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/*

 */
#[HexbatchTitle( title: "Destroy an element")]
#[HexbatchBlurb( blurb: "Can destroy one or more elements of the same type")]
#[HexbatchDescription( description:'
 * it is ok if an element is destroyed while things are working on it,
 it will just fail those things, or they will finish without it.

 No permission is needed for an owner or admin group to destroy them, the notices are sent out after the fact
')]
class ElementDestroy extends Act\Cmd\Ele
{
    const UUID = '557bbc2e-f589-4874-91f0-5d5e96fe115f';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_DESTROY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

    const EVENT_CLASSES = [
        Evt\Type\ElementDestruction::class,
        Evt\Type\ElementDestructionBatch::class
    ];

}

