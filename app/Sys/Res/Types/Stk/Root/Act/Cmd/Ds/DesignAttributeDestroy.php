<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;

#[HexbatchTitle( title: "Destroys an attribute")]
#[HexbatchBlurb( blurb: "Attributes can be destroyed while in design phase. If type has approving parents, this is set back to pending and they are notified")]
#[HexbatchDescription( description:'')]
class DesignAttributeDestroy extends Act\Cmd\Ds
{
    const UUID = '079cfc62-0fa2-47f1-84c0-df0fa90441c5';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_DESTROY;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

