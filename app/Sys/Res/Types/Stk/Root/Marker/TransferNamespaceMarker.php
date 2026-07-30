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
class TransferNamespaceMarker extends Marker
{
    const UUID = '66ff3f14-5676-497c-8946-d6a15b66848a';
    const TYPE_NAME = 'transfer_marker';
    const bool IS_FINAL = true;

    const ATTRIBUTE_CLASSES = [];


    const PARENT_CLASSES = [
        Marker::class
    ];

}

