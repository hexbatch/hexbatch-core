<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


#[HexbatchTitle( title: "Deletes a schedule")]
#[HexbatchBlurb( blurb: "Schdules can be removed if not used by any published type")]
#[HexbatchDescription( description:'')]
class DesignTimeDestroy extends Act\Cmd\Ds
{
    const UUID = '1f104a48-34f4-4338-9723-a62fccbbe83a';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_TIME_DESTROY;
//
    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

