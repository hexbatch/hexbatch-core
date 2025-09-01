<?php

namespace App\OpenApi\Common\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'HexbatchUuidUuid',
    title: 'Namespaced name with both in uuid format',
    description: 'Defines a uuid:uuid',
    type: 'string',
    maxLength: 36 + 1 + 36,
    minLength: 36 + 1 + 36,
    pattern: '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}:[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
    example: [new OA\Examples(summary: "A uuid with a colon then a uuid", value:'3fabcde3-aaaa-41b0-88c6-3d3f45e83bf4:3fabcde3-e732-41b0-88c6-3d3f45e83bf4') ]
)]
class HexbatchUuidUuid
{


}
