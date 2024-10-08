<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Meta;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class Url extends BaseAttribute
{
    const UUID = 'f522ecf8-2bb4-475a-b9df-0317c244bdf3';
    const ATTRIBUTE_NAME = 'url';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


