<?php

namespace App\OpenApi\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_namespaced_resource',
    title: 'Namespaced name',
    description: 'Defines a namespace.name',
    type: 'string',
    maxLength: 61,
    minLength: 7,
    pattern: '^\p{L}[\p{L}0-9_]{2,29}\.\p{L}[\p{L}0-9_]{2,29}$',
    example: [new OA\Examples(summary: "A namespace with a dot then a resource name", value:'cats.first_kittens99') ]
)]
class HexbatchNamespacedName
{


}
