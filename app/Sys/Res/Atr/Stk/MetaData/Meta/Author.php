<?php

namespace App\Sys\Res\Atr\Stk\MetaData\Meta;



use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Sys\Res\Atr\Stk\MetaData\Metadata;
#[HexbatchBlurb( blurb: "Meant to mark the author")]
#[HexbatchDescription( description:'
# Unstructured author data goes here

Later on will make a json spec to read and write

')]
class Author extends Metadata
{
    const UUID = '613bbe91-dc7b-42c0-9c0a-b6c13c8aa2be';
    const ATTRIBUTE_NAME = 'author';
    const PARENT_ATTRIBUTE_CLASS = Metadata::class;


}


