<?php

namespace App\OpenApi\Common\Resources\Attributes;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Attribute',
    description: 'Defines a namespace:type:attribute',
    type: 'string',
    maxLength: 36 + 1 + 30 + 1 + 30,
    minLength: 36 + 1 +  3 + 1 + 3,
    pattern: '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}:\p{L}[\p{L}0-9_]{2,29}:\p{L}[\p{L}0-9_]{2,29}$',
    example: [new OA\Examples(summary: "A uuid  with a colon then a type name  with a colon then an attribute", value:'5feda9e3-e732-41b0-88c6-3d3f45e83bf4:feral:color') ]
)]
class HexbatchUuidTypeAttribute
{


}
