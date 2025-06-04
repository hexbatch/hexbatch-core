<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;

#[HexbatchTitle( title: "Edit an attribute")]
#[HexbatchBlurb( blurb: "Change an existing attribute, no events are created. Reviews by others can be done when publishing")]
#[HexbatchDescription( description:'
# Editing an attribute.

The only time an attribute is editable is in design mode.

see  [DesignAttributeCreate](DesignAttributeCreate.php) for the argument list

Extra argument here is

* design_attribute_uuid : identify the attribute to be edited with the uuid

')]
class DesignAttributeEdit extends DesignAttributeCreate
{
    const UUID = 'b5dc244c-d966-48fd-9c42-ed53cceb827f';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_EDIT;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\DesignPending::class
    ];



}

