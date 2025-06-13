<?php

namespace App\OpenApi\Resources\Attributes;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_namespace_type_attribute',
    title: 'Attribute',
    description: 'Defines a namespace.type.attribute',
    type: 'string',
    maxLength: 61,
    minLength: 7,
    pattern: '^\p{L}[\p{L}0-9_]{2,29}\.\p{L}[\p{L}0-9_]{2,29}\.\p{L}[\p{L}0-9_]{2,29}$',
    example: [new OA\Examples(summary: "A  namespace with a dot then a type name  with a dot then an attribute", value:'cats.feral.color') ]
)]
class HexbatchNamespaceTypeAttribute
{


}
