<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;


#[HexbatchTitle( title: "Designs can be edited")]
#[HexbatchBlurb( blurb: "If approving parents, this is set back to pending and they are notified")]
#[HexbatchDescription( description:'')]

class DesignEdit extends Act\Cmd\Ds
{
    const UUID = '9f0285dc-0af5-4176-b82d-ac930d93b132';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_EDIT;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

