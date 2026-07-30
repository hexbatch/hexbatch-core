<?php

namespace App\Sys\Res\Types\Stk\Root\Marker;




use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Sys\Res\Types\Stk\Root\Marker;
#[HexbatchTitle( title: "Marker for deletion")]
#[HexbatchBlurb( blurb: "This is put here during the pre-delete operation for namespaces")]
#[HexbatchDescription( description: "
  When an element of this is inside the homeset, the namespace can be deleted. Default namespaces cannot be deleted
")]
class DeletingNamespaceMarker extends Marker
{
    const UUID = '244663d2-7d47-4497-b8af-9efde3c5d7e9';
    const TYPE_NAME = 'deletion_marker';

    const bool IS_FINAL = true;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Marker::class
    ];

}

