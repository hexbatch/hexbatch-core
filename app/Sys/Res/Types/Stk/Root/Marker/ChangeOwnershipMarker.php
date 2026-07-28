<?php

namespace App\Sys\Res\Types\Stk\Root\Marker;




use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Sys\Res\Types\Stk\Root\Marker;

#[HexbatchTitle( title: "Marker for changing owner")]
#[HexbatchBlurb( blurb: "This is put here during the pre-change operation for namespaces")]
#[HexbatchDescription( description: "
  When an element of this is inside the homeset, the namespace can be transfered to another user. Default namespaces cannot change ownership
")]
class ChangeOwnershipMarker extends Marker
{
    const UUID = 'cc847fa8-2fb6-4ed1-8c29-95bb4e0f1f0f';
    const TYPE_NAME = 'change_ownership_marker';



    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Marker::class
    ];

}

