<?php

namespace App\OpenApi\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_namespaced_uuid',
    title: 'Namespaced uuid',
    description: 'Defines a namespace:uuid',
    type: 'string',
    maxLength: 30 + 1 + 36,
    minLength: 3 + 1 + 36,
    pattern: '^\p{L}[\p{L}0-9_]{2,29}:[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
    example: [new OA\Examples(summary: "A namespace with a colon then a uuid", value:'cats:3fabcde3-e732-41b0-88c6-3d3f45e83bf4') ]
)]
class HexbatchNamespacedUuid
{


}
