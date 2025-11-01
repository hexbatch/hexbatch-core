<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\OpenApi\Params\Actioning\Design\DesignLocationParams;
use App\Sys\Res\Types\Stk\Root\Act;

#[HexbatchTitle( title: "Edit location bounds")]
#[HexbatchBlurb( blurb: "Can edit all if not used in any published types, otherwise only display")]
#[HexbatchDescription( description:'See location create')]
#[ApiParamMarker( param_class: DesignLocationParams::class)]
class DesignLocationEdit extends DesignLocationCreate
{
    const UUID = '28057dde-2273-4a28-a07d-ecf9b8934c08';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_LOCATION_EDIT;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];

}

