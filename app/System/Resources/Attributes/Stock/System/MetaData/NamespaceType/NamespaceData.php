<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\NamespaceType;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class NamespaceData extends BaseAttribute
{
    const UUID = 'e52f89b6-b143-41ab-928c-7e750d2fe302';
    const ATTRIBUTE_NAME = 'namespace_data';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


