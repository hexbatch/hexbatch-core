<?php

namespace App\OpenApi\Resources\Attributes;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_domain_type_uuid',
    title: 'Attribute',
    description: 'Defines a domain:type:uuid',
    type: 'string',
    maxLength: 253 + 1 + 30 + 1 + 36,
    minLength: 3 + 1 + 3 + 1 + 36,
    pattern: '^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]:\p{L}[\p{L}0-9_]{2,29}:[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
    example: [new OA\Examples(summary: "A domain with a colon then a type name  with a colon then a uuid", value:'top.example.com:feral:3fabcde3-e732-41b0-88c6-3d3f45e83bf4') ]
)]
class HexbatchDomainTypeUuid
{


}
