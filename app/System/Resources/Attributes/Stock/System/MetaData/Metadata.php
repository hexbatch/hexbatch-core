<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData;



use App\System\Resources\Attributes\BaseAttribute;

class Metadata extends BaseAttribute
{
    const UUID = 'd84561f0-8713-4ae1-922b-f548cdd8e7c7';
    const ATTRIBUTE_NAME = 'metadata';

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


