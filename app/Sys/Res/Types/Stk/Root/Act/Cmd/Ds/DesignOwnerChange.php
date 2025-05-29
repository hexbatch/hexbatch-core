<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


#[HexbatchTitle( title: "Change the ownership of a design")]
#[HexbatchBlurb( blurb: "Unpublished designs have their ownership changed here, this can be refused by the otherwise new owner")]
#[HexbatchDescription( description:'')]
class DesignOwnerChange extends Act\Cmd\Ds
{
    const UUID = '3baa3285-5dff-42b5-bd22-071ad39101db';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_OWNER_CHANGE;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

    const EVENT_CLASSES = [
        Evt\Server\TypeOwnerChange::class
    ];

}

