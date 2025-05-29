<?php

namespace App\Sys\Res\Atr\Stk\MetaData;



use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Sys\Res\Atr\BaseAttribute;

#[HexbatchTitle( title: "Metadata")]
#[HexbatchBlurb( blurb: "This and children attributes hold meta information")]
#[HexbatchDescription( description: "

  There is a wide variety of meta information, one can use the more derived attributes or use from here to make a new class of meta
")]
class Metadata extends BaseAttribute
{
    const UUID = 'd84561f0-8713-4ae1-922b-f548cdd8e7c7';
    const ATTRIBUTE_NAME = 'metadata';


}


