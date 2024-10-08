<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Meta;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class StartsAt extends BaseAttribute
{
    const UUID = 'e2cd7771-0fc5-4399-9e02-2d8a1af20840';
    const ATTRIBUTE_NAME = 'starts_at';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


