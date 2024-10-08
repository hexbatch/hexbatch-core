<?php

namespace App\System\Resources\Attributes\Stock;



use App\System\Resources\Attributes\BaseAttribute;

class Expiration extends BaseAttribute
{
    const UUID = '3b5e9d42-672c-4e9b-9b17-efaec6fdd04c';
    const ATTRIBUTE_NAME = 'expiration';

    public function getAttributeName(): string { return static::ATTRIBUTE_NAME;}

    public function getAttributeData(): ?array
    {
        return null;
    }
}


