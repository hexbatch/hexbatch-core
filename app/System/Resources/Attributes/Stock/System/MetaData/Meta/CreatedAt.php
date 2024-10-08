<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Meta;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class CreatedAt extends BaseAttribute
{
    const UUID = '07441f00-8c3f-4fdb-a13c-5a140b1a668a';
    const ATTRIBUTE_NAME = 'created_at';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


