<?php

namespace App\OpenApi\Common\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'HexbatchDomainedName',
    title: 'Domained name',
    description: 'Defines a domain:name',
    type: 'string',
    maxLength: 253 + 1 + 30,
    minLength: 3 + 1 + 3,
    pattern: '^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]:\p{L}[\p{L}0-9_]{2,29}$',
    example: [new OA\Examples(summary: "A domain or subdomain with a colon then a resource name", value:'top.example.com:first_kittens99') ]
)]
class HexbatchDomainedName
{


}
