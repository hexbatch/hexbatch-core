<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;

/**
 * Can be given another type to copy the schedule from
 */
#[HexbatchTitle( title: "Destroys a schdule")]
#[HexbatchBlurb( blurb: "Time bounds can removed if not used by any published type")]
#[HexbatchDescription( description:'')]
class DesignLocationDestroy extends Act\Cmd\Ds
{
    const UUID = 'f6986ecb-de5e-4551-86cf-2cbc855b9780';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LOCATION_DESTROY;

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

