<?php

namespace App\OpenApi\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_uuid',
    title: 'Uuid',
    description: 'Defines a uuid format accepted',
    type: 'string',
    maxLength: 36,
    minLength: 36,
    pattern: '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
    example: [new OA\Examples(summary: "Example uuid", value:'3fabcde3-e732-41b0-88c6-3d3f45e83bf4') ]
)]
class HexbatchUuid
{


}
