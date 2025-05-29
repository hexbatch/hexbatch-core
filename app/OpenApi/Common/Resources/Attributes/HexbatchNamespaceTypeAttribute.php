<?php

namespace App\OpenApi\Common\Resources\Attributes;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Attribute',
    description: 'Defines a namespace:type:attribute',
    type: 'string',
    maxLength: 30 + 1 + 30 + 1 + 30,
    minLength: 3 + 1 + 3 + 1 + 3,
    pattern: '^\p{L}[\p{L}0-9_]{2,29}:\p{L}[\p{L}0-9_]{2,29}:\p{L}[\p{L}0-9_]{2,29}$',
    example: [new OA\Examples(summary: "A  namespace with a colon then a type name  with a colon then an attribute", value:'cats:feral:color') ]
)]
class HexbatchNamespaceTypeAttribute
{


}
