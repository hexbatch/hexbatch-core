<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Meta;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class IsoLanguage extends BaseAttribute
{
    const UUID = 'b845b935-a0b5-486a-b163-94c23ee1c503';
    const ATTRIBUTE_NAME = 'iso_language';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


