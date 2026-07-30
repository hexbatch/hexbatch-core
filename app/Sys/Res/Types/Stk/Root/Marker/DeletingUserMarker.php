<?php

namespace App\Sys\Res\Types\Stk\Root\Marker;

use App\Sys\Res\Types\Stk\Root\Marker;


class DeletingUserMarker extends Marker
{
    const UUID = '5ac098bd-7ee6-421e-bff8-849d20a60bfb';
    const TYPE_NAME = 'delete_user_marker';

    const bool IS_FINAL = true;
    const ATTRIBUTE_CLASSES = [];


    const PARENT_CLASSES = [
        Marker::class
    ];

}

