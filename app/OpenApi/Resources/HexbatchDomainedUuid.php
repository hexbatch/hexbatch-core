<?php

namespace App\OpenApi\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_domained_uuid',
    title: 'Domained uuid',
    description: 'Defines a domain:uuid',
    type: 'string',
    maxLength: 253 + 1 + 36,
    minLength: 3 + 1 + 36,
    pattern: '^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]:[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
    example: [new OA\Examples(summary: "A domain or subdomain with a colon then a uuid", value:'top.example.com:3fabcde3-e732-41b0-88c6-3d3f45e83bf4') ]
)]
class HexbatchDomainedUuid
{


}
