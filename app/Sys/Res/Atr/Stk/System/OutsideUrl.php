<?php

namespace App\Sys\Res\Atr\Stk\System;



use App\Sys\Res\Atr\BaseAttribute;

class OutsideUrl extends BaseAttribute
{
    const UUID = 'f25c5924-5fe5-48a1-9e64-7380f07b8752';
    const ATTRIBUTE_NAME = 'outside_url';


}

/*
 urls for image, svg, file checking will be done in the next layer, where it downloads and scans, accepting or rejecting it,
        the system user can hook into the descendants of the url attributes to deny the value change or accept it.
 */


