<?php

namespace App\OpenApi\Common\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'HexbatchAttribute',
    title: 'Attribute that is defined by a namespace:type:attribute in name or uuid parts ',
    description: 'Names an attribute',
    type: 'string',
    maxLength: 253 + 1 + 36 + 1 + 36,
    minLength: 3 + 1 + 3 + 1 + 3,
    example: [
        new OA\Examples(summary: "A type with a colon then a uuid", value:'cats:3fabcde3-e732-41b0-88c6-3d3f45e83bf4'),
        new OA\Examples(summary: "A uuid with a colon then an attribute", value:'3fabcde3-aaaa-41b0-88c6-3d3f45e83bf4:3fabcde3-e732-41b0-88c6-3d3f45e83bf4'),
        new OA\Examples(summary: "A type with a colon then an attribute", value:'cats:first_kittens99'),

        new OA\Examples(summary: "A  namespace with a colon then a type name  with a colon then an attribute", value:'cats:feral:color'),
        new OA\Examples(summary: "A  namespace with a colon then a type name  with a colon then a uuid", value:'cats:feral:3fabcde3-e732-41b0-88c6-3d3f45e83bf4'),
        new OA\Examples(summary: "A  namespace with a colon then a uuid  with a colon then an attribute", value:'cats:3fabcde3-e732-41b0-88c6-3d3f45e83bf4:color'),
        new OA\Examples(summary: "A  namespace with a colon then a uuid with a colon then a uuid", value:'cats:4feda9e3-e732-41b0-88c6-3d3f45e83bf4:3fabcde3-e732-41b0-88c6-3d3f45e83bf4'),
        new OA\Examples(summary: "A uuid  with a colon then a type name  with a colon then an attribute", value:'5feda9e3-e732-41b0-88c6-3d3f45e83bf4:feral:color'),
        new OA\Examples(summary: "A uuid with a colon then a type name  with a colon then a uuid", value:'5feda9e3-e732-41b0-88c6-3d3f45e83bf4:feral:3fabcde3-e732-41b0-88c6-3d3f45e83bf4'),
        new OA\Examples(summary: "A uuid with a colon then a uuid  with a colon then an attribute", value:'5feda9e3-e732-41b0-88c6-3d3f45e83bf4:3fabcde3-e732-41b0-88c6-3d3f45e83bf4:color'),
        new OA\Examples(summary: "A uuid with a colon then a uuid with a colon then a uuid", value:'5feda9e3-e732-41b0-88c6-3d3f45e83bf4:4feda9e3-e732-41b0-88c6-3d3f45e83bf4:3fabcde3-e732-41b0-88c6-3d3f45e83bf4'),


        new OA\Examples(summary: "A domain with a colon then a type name  with a colon then an attribute", value:'top.example.com:feral:color'),
        new OA\Examples(summary: "A domain with a colon then a type name  with a colon then a uuid", value:'top.example.com:feral:3fabcde3-e732-41b0-88c6-3d3f45e83bf4'),
        new OA\Examples(summary: "A domain with a colon then a uuid  with a colon then an attribute", value:'top.example.com:3fabcde3-e732-41b0-88c6-3d3f45e83bf4:color'),
        new OA\Examples(summary: "A domain with a colon then a uuid with a colon then a uuid", value:'top.example.com:4feda9e3-e732-41b0-88c6-3d3f45e83bf4:3fabcde3-e732-41b0-88c6-3d3f45e83bf4')

    ],
    oneOf: [

        new OA\Schema(ref: '#/components/schemas/HexbatchNamespacedUuid'),
        new OA\Schema( ref: '#/components/schemas/HexbatchUuidUuid'),
        new OA\Schema(ref: '#/components/schemas/HexbatchNamespacedName'),


        new OA\Schema(ref: '#/components/schemas/HexbatchNamespaceTypeAttribute'),
        new OA\Schema(ref: '#/components/schemas/HexbatchNamespaceTypeUuid' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchNamespaceUuidAttribute' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchNamespaceUuidUuid' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchUuidTypeAttribute' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchUuidTypeUuid' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchUuidUuidAttribute' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchUuidUuidUuid' ),

        new OA\Schema(ref: '#/components/schemas/HexbatchDomainTypeAttribute' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchDomainTypeUuid' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchDomainUuidAttribute' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchDomainUuidUuid' ),
    ]
)]
class HexbatchAttribute
{


}
