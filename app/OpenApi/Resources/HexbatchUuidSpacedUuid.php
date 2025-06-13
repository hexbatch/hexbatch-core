<?php

namespace App\OpenApi\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_namespaced_resource',
    title: 'Namespaced name with both in uuid format',
    description: 'Defines a uuid.uuid',
    type: 'string',
    maxLength: 61,
    minLength: 7,
    pattern: '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}\.[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
    example: [new OA\Examples(summary: "A uuid with a dot then a uuid", value:'3fabcde3-aaaa-41b0-88c6-3d3f45e83bf4.3fabcde3-e732-41b0-88c6-3d3f45e83bf4') ]
)]
class HexbatchUuidSpacedUuid
{


}
