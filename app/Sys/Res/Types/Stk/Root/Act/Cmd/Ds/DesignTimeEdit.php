<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;

#[HexbatchTitle( title: "Edit schedule")]
#[HexbatchBlurb( blurb: "Schedules can be fully edited while in design phase without events, or if published without approving parents")]
#[HexbatchDescription( description:'')]
class DesignTimeEdit extends Act\Cmd\Ds
{
    const UUID = '3e223f14-621b-4ba2-8d93-4822c07f727d';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LOCATION_EDIT;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

