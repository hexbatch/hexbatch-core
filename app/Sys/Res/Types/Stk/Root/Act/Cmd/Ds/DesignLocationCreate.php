<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;

#[HexbatchTitle( title: "Create a location")]
#[HexbatchBlurb( blurb: "Create a 2d map bounds or a 3d shape")]
#[HexbatchDescription( description:'')]
class DesignLocationCreate extends Act\Cmd\Ds
{
    const UUID = 'f26dcdcb-09e4-41df-b435-3e7b106c6282';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LOCATION_CREATE;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

