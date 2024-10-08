<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Meta;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class Author extends BaseAttribute
{
    const UUID = '613bbe91-dc7b-42c0-9c0a-b6c13c8aa2be';
    const ATTRIBUTE_NAME = 'author';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


