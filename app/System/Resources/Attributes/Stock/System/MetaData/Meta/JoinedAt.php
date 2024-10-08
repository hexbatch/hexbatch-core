<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Meta;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class JoinedAt extends BaseAttribute
{
    const UUID = 'cc281e79-aff8-4332-9981-98e732c7c9fd';
    const ATTRIBUTE_NAME = 'joined_at';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


