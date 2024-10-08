<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Meta;



use App\System\Resources\Attributes\BaseAttribute;

class Privacy extends BaseAttribute
{
    const UUID = 'ef204153-3ad1-4c74-b040-7f4d0489f5b6';
    const ATTRIBUTE_NAME = 'privacy';
    const PARENT_UUID = Url::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


