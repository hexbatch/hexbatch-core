<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt\Set\AttributeWrite;

/**
* @see AttributeWrite event on the type

 */
#[HexbatchTitle( title: "Deletes a design by system")]
#[HexbatchBlurb( blurb: "No permission checks, no events raised")]
#[HexbatchDescription( description:'
if there is one or more elements who have a matching live type (or base live type)
 then pick at most one element and write matching attributes to that live type

 The pinging element does not become part of the set.
 There is only a @see AttributeWrite event on the type
 and no set events

 If ping a set, and no match, do not try down-set (use search to find ping targets)

 nothing can stop the ping, cannot be filtered out

 After ping, either the element is destroyed or goes back into a ping set to be used again
 (if set inheriting from the ping type is existing its set, otherwise destroy)
')]
class ElementPing extends Act\Cmd\Ele
{
    const UUID = '54e60992-c545-4280-9469-b1c02e0f6fc5';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_PING;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class
    ];

}

