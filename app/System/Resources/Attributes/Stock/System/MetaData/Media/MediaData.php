<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Media;



use App\System\Resources\Attributes\BaseAttribute;
use App\System\Resources\Attributes\Stock\System\MetaData\Metadata;

class MediaData extends BaseAttribute
{
    const UUID = 'f79f8aa2-ad16-4e9a-80ca-895b2dff73ee';
    const ATTRIBUTE_NAME = 'media_data';
    const PARENT_UUID = Metadata::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


