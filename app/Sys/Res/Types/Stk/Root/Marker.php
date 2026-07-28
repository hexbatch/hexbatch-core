<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Sys\Res\Atr\Stk\Placeholder\MarkerData;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

#[HexbatchTitle( title: "Marker base")]
#[HexbatchBlurb( blurb: "Some actions require confirmation by the owner, the descendants of this marks the acknowledgement")]
#[HexbatchDescription( description: "
  Each child class has its own use. Its important that the data here is per element and can only be seen by the owner admin group
")]
class Marker extends BaseType
{
    const UUID = 'fdf358b2-391a-45f7-a4b2-3ea7a7cc23f8';
    const TYPE_NAME = 'marker';

    const TypeOfServerAccess ACCESS_POLICY = TypeOfServerAccess::IS_PRIVATE;
    const ATTRIBUTE_CLASSES = [
        MarkerData::class
    ];

    const PARENT_CLASSES = [
        Root::class
    ];



}

