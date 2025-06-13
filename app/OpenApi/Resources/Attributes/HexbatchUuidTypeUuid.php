<?php

namespace App\OpenApi\Resources\Attributes;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_namespace_type_uuid',
    title: 'Attribute',
    description: 'Defines a namespace.type.uuid',
    type: 'string',
    maxLength: 61,
    minLength: 7,
    pattern: '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}\.\p{L}[\p{L}0-9_]{2,29}\.[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
    example: [new OA\Examples(summary: "A uuid with a dot then a type name  with a dot then a uuid", value:'5feda9e3-e732-41b0-88c6-3d3f45e83bf4.feral.3fabcde3-e732-41b0-88c6-3d3f45e83bf4') ]
)]
class HexbatchUuidTypeUuid
{


}
