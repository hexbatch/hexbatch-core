<?php

namespace App\OpenApi\Common\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_namespace',
    title: 'A namespace ',
    description: 'A namespace has the same rules as a resource, and is used to organize resources',
    type: 'string',
    items: new OA\Items(
        oneOf: [
            new OA\Schema(ref: HexbatchResourceName::class),
            new OA\Schema(ref: HexbatchUuid::class)
        ],
    ),
    maxLength: 36,
    minLength: 3,
    example: [
        new OA\Examples(summary: "A name by itself", value:'my_first_user221'),
        new OA\Examples(summary: "A uuid by itself", value:'3fabcde3-e732-41b0-88c6-3d3f45e83bf4')
    ]
)]
class HexbatchNamespace
{


}
