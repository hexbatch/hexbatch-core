<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


#[HexbatchTitle( title: "Add or replace handle")]
#[HexbatchBlurb( blurb: "Set the handle for a published type")]
#[HexbatchDescription( description:'
Handles allow grouping of api calls

')]
class TypeHandleAdd extends Act\Cmd\Ty
{
    const UUID = 'c79c2c70-f92e-4fdc-9cff-db756f8a1c8b';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_HANDLE_ADD;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Ty::class
    ];


    const EVENT_CLASSES = [
        Evt\Server\TypeHandleAdded::class
    ];

}

