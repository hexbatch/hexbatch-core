<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\OpenApi\Params\Element\ElementSelectParams;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


#[HexbatchTitle( title: "Turn on a type in an element")]
#[HexbatchBlurb( blurb: "Turns on all the attributes of a subtype in an element")]
#[HexbatchDescription( description: '
  # When attributes are toggled on

  Attributes are organized by type, and subtypes of an element can be turned on and off for that element.
  This command turns on a type in an element

  If no event handlers, then the element admin group AND
  a check for the caller being associated with each attribute in the type.

   * if the attribute is public domain no check
   * if attribute public or protected then must be a member of the type
   * if attribute private then must be an admin of the type

   But, event handling can be used. Each element owner and type owner is sent
   * [ElementTypeTurningOn](../../../Evt/Set/ElementTypeTurningOn.php)

   if all agree, then the type is turned on for that element

   and the element owner and type owners, and anyone else listening gets the following

   * [ElementTypeTurnedOn](../../../Evt/Set/ElementTypeTurnedOn.php)
')]
#[ApiParamMarker( param_class: ElementSelectParams::class)]
class TypeOn extends TypeOff
{
    const UUID = '2d0a931a-be5a-4cab-b177-c9e9ec78e432';
    const ACTION_NAME = TypeOfAction::PRAGMA_TYPE_ON;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ele::class,
        Act\Pragma::class
    ];

    const EVENT_CLASSES = [
        Evt\Set\ElementTypeTurningOn::class,
        Evt\Set\ElementTypeTurnedOn::class,
    ];

    const bool MAKING_VISIBLE = true;

    const PRE_EVENT_CLASS = Evt\Set\ElementTypeTurningOn::class;
    const POST_EVENT_CLASS = Evt\Set\ElementTypeTurnedOn::class;

}

