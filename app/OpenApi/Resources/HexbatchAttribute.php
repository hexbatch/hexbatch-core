<?php

namespace App\OpenApi\Resources;

use App\OpenApi\Resources\Attributes\HexbatchDomainTypeAttribute;
use App\OpenApi\Resources\Attributes\HexbatchDomainTypeUuid;
use App\OpenApi\Resources\Attributes\HexbatchDomainUuidAttribute;
use App\OpenApi\Resources\Attributes\HexbatchDomainUuidUuid;
use App\OpenApi\Resources\Attributes\HexbatchNamespaceTypeAttribute;
use App\OpenApi\Resources\Attributes\HexbatchNamespaceTypeUuid;
use App\OpenApi\Resources\Attributes\HexbatchNamespaceUuidAttribute;
use App\OpenApi\Resources\Attributes\HexbatchNamespaceUuidUuid;
use App\OpenApi\Resources\Attributes\HexbatchUuidTypeAttribute;
use App\OpenApi\Resources\Attributes\HexbatchUuidTypeUuid;
use App\OpenApi\Resources\Attributes\HexbatchUuidUuidAttribute;
use App\OpenApi\Resources\Attributes\HexbatchUuidUuidUuid;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_attribute',
    title: 'Attribute that is defined by a namespace:type:attribute in name or uuid parts ',
    description: 'Names an attribute',
    type: 'string',
    items: new OA\Items(
        oneOf: [

            new OA\Schema(ref: HexbatchNamespacedUuid::class),
            new OA\Schema(ref: HexbatchUuidUuid::class),
            new OA\Schema(ref: HexbatchNamespacedName::class),


            new OA\Schema(ref: HexbatchNamespaceTypeAttribute::class),
            new OA\Schema(ref: HexbatchNamespaceTypeUuid::class),
            new OA\Schema(ref: HexbatchNamespaceUuidAttribute::class),
            new OA\Schema(ref: HexbatchNamespaceUuidUuid::class),
            new OA\Schema(ref: HexbatchUuidTypeAttribute::class),
            new OA\Schema(ref: HexbatchUuidTypeUuid::class),
            new OA\Schema(ref: HexbatchUuidUuidAttribute::class),
            new OA\Schema(ref: HexbatchUuidUuidUuid::class),

            new OA\Schema(ref: HexbatchDomainTypeAttribute::class),
            new OA\Schema(ref: HexbatchDomainTypeUuid::class),
            new OA\Schema(ref: HexbatchDomainUuidAttribute::class),
            new OA\Schema(ref: HexbatchDomainUuidUuid::class),
        ],
    ),
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

    ]
)]
class HexbatchAttribute
{


}
