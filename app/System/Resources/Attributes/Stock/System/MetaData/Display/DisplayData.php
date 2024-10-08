<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Display;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class DisplayData extends BaseAttribute
{
    const UUID = '23dab6bb-bd14-4597-ba27-63c7209f1b10';
    const ATTRIBUTE_NAME = 'display_data';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


