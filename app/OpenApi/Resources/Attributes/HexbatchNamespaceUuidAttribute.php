<?php

namespace App\OpenApi\Resources\Attributes;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_namespace_uuid_attribute',
    title: 'Attribute',
    description: 'Defines a namespace.uuid.attribute',
    type: 'string',
    maxLength: 61,
    minLength: 7,
    pattern: '^\p{L}[\p{L}0-9_]{2,29}\.[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}\.\p{L}[\p{L}0-9_]{2,29}$',
    example: [new OA\Examples(summary: "A  namespace with a dot then a uuid  with a dot then an attribute", value:'cats.3fabcde3-e732-41b0-88c6-3d3f45e83bf4.color') ]
)]
class HexbatchNamespaceUuidAttribute
{


}
