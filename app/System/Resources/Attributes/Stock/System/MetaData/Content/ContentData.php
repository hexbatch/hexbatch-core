<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Content;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class ContentData extends BaseAttribute
{
    const UUID = '0af24849-9f7d-4492-bc36-4ea29d7a9ee1';
    const ATTRIBUTE_NAME = 'content_data';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


