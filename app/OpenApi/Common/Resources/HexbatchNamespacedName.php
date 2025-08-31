<?php

namespace App\OpenApi\Common\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_namespaced_name',
    title: 'Namespaced name',
    description: 'Defines a namespace:name',
    type: 'string',
    maxLength: 30 + 1 + 30,
    minLength: 3 + 1 + 3,
    pattern: '^\p{L}[\p{L}0-9_]{2,29}:\p{L}[\p{L}0-9_]{2,29}$',
    example: [new OA\Examples(summary: "A namespace with a colon then a resource name", value:'cats:first_kittens99') ]
)]
class HexbatchNamespacedName
{


}
