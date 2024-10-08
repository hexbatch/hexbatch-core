<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Meta;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class IsoRegion extends BaseAttribute
{
    const UUID = '2647ee63-0c9a-4090-8118-266dadd9f573';
    const ATTRIBUTE_NAME = 'iso_region';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


