<?php


namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Atr\Stk\MetaData;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;


class Meta extends BaseType
{
    const UUID = 'f675592e-dacd-471e-a679-971a09c00b4d';
    const TYPE_NAME = 'meta';



    const ATTRIBUTE_CLASSES = [
        MetaData\Metadata::class,
        Metadata\Meta\About::class,
        Metadata\Meta\Author::class,
        Metadata\Meta\Copywrite::class,
        Metadata\Meta\CreatedAt::class,
        Metadata\Meta\EndsAt::class,
        Metadata\Meta\IsoLanguage::class,
        Metadata\Meta\IsoRegion::class,
        Metadata\Meta\JoinedAt::class,
        Metadata\Meta\Keywords::class,
        Metadata\Meta\MimeType::class,
        Metadata\Meta\Privacy::class,
        Metadata\Meta\Rating::class,
        Metadata\Meta\StartsAt::class,
        Metadata\Meta\Terms::class,
        Metadata\Meta\UpdatedAt::class,
        Metadata\Meta\MetaUrl::class,

    ];

    const PARENT_CLASSES = [
        Root::class
    ];

}
