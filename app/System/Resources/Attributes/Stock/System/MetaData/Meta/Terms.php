<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Meta;



use App\System\Resources\Attributes\BaseAttribute;

class Terms extends BaseAttribute
{
    const UUID = '0316226a-52ea-4394-bcb8-f609317947d4';
    const ATTRIBUTE_NAME = 'terms';
    const PARENT_UUID = Url::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


