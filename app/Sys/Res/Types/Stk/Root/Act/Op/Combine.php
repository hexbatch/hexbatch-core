<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Op;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

/*
 (p)A op (q)B => C

  p provides context if we are working with all e of selected types, selected e or other, can mix and match
  C can be A or B
  use any logic, it is used on each type or element in A, B (false if missing, true if there) logic to put in C
  if C is A or B, and the logic is false for that element|type then it is removed from C
   what is true for each type in the path will
  to remove then filter of some other thing that produces/returns a set

when an element is removed from its last set, it is automatically destroyed

when adding or removing elements, can to be cancelled with the enter and leave set events
also, need to pay attention to the clipping of the location bounds, the clipping also applies to up-set boundaries too

after all permissions given, for each remaining removal,
  do the command for cmd_set_member_remove_no_events for remove
  and cmd_set_member_add_no_events for those being added
  which are parents for the events above

the combine does not look at subtypes fitting into the desired set, it simply adds or removes.
However, once in, not related to this command, those subtype values can be turned off if not fit

 */
class Combine extends Act\Op
{
    const UUID = 'c8833a43-8e2a-4a88-995f-f27c816dc073';
    const ACTION_NAME = TypeOfAction::OP_COMBINE;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Op::UUID
    ];

    const EVENT_UUIDS = [
        Evt\Set\SetEnter::UUID,
        Evt\Set\SetLeave::UUID,
    ];

}

