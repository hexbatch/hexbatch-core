<?php

namespace App\OpenApi\Common\Resources\Attributes;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Attribute',
    description: 'Defines a domain:uuid:attribute',
    type: 'string',
    maxLength: 253 + 1 + 36 + 1 + 30,
    minLength: 3 + 1 + 36  + 1 + 3,
    pattern: '^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]:[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}:\p{L}[\p{L}0-9_]{2,29}$',
    example: [new OA\Examples(summary: "A domain with a colon then a uuid  with a colon then an attribute", value:'top.example.com:3fabcde3-e732-41b0-88c6-3d3f45e83bf4:color') ]
)]
class HexbatchDomainUuidAttribute
{


}
