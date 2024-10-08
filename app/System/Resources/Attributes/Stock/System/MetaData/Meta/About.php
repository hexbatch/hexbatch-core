<?php

namespace App\System\Resources\Attributes\Stock\System\MetaData\Meta;



use App\System\Resources\Attributes\BaseAttribute;

class About extends BaseAttribute
{
    const UUID = 'ca26945a-1fa5-443b-9615-857c07440561';
    const ATTRIBUTE_NAME = 'about';
    const PARENT_UUID = Url::UUID;

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


