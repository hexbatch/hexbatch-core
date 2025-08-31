<?php

namespace App\OpenApi\Common\Resources\Attributes;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_domain_type_attribute',
    title: 'Attribute',
    description: 'Defines a domain:type:attribute',
    type: 'string',
    maxLength: 253 + 1 + 30 + 1 + 30,
    minLength: 3 + 1 + 3 + 1 + 3,
    pattern: '^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]:\p{L}[\p{L}0-9_]{2,29}:\p{L}[\p{L}0-9_]{2,29}$',
    example: [new OA\Examples(summary: "A domain with a colon then a type name  with a colon then an attribute", value:'top.example.com:feral:color') ]
)]
class HexbatchDomainTypeAttribute
{


}
