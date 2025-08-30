<?php

namespace App\OpenApi\Common;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_uuid',
    title: 'Unique reference',
    description: 'Defines a regular uuid',
    type: 'string',
    maxLength: 36,
    minLength: 36,
    pattern: '^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$',
    example: [new OA\Examples(summary: "Should be unique across the world", value:'59714b32-a017-4300-afbc-f910886a2589') ]
)]
class HexbatchUuid
{


}
