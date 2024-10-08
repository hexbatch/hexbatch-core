<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Meta;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class UpdatedAt extends BaseAttribute
{
    const UUID = '3be16cb1-33f8-4fd4-a313-e3ec9c9e6542';
    const ATTRIBUTE_NAME = 'updated_at';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


