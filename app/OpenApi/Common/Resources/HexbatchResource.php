<?php

namespace App\OpenApi\Common\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'HexbatchResource',
    title: 'Resource Name or uuid that can be namespaced ',
    description: 'Names a resource',
    type: 'string',
    maxLength: 253 + 1 + 36,
    minLength: 3,
    example: [
        new OA\Examples(summary: "A name by itself", value:'my_first_user221'),
        new OA\Examples(summary: "A name with a namespace", value:'cats:envy'),
        new OA\Examples(summary: "A uuid by itself", value:'3fabcde3-e732-41b0-88c6-3d3f45e83bf4'),
        new OA\Examples(summary: "A uuid with a namespace", value:'cats:3fabcde3-e732-41b0-88c6-3d3f45e83bf4'),
        new OA\Examples(summary: "A uuid with a namespace in uuid form", value:'3fabcde3-aaaa-41b0-88c6-3d3f45e83bf4:3fabcde3-e732-41b0-88c6-3d3f45e83bf4'),
        new OA\Examples(summary: "A domain or subdomain with a colon then a uuid", value:'top.gun.org:3fabcde3-e732-41b0-88c6-3d3f45e83bf4'),
        new OA\Examples(summary: "A domain or subdomain with a colon then a resource name", value:'top.gun.org:first_kittens99')
    ],
    oneOf: [
        new OA\Schema(ref: '#/components/schemas/HexbatchResourceName'),
        new OA\Schema(ref: '#/components/schemas/HexbatchUuid'),
        new OA\Schema(ref: '#/components/schemas/HexbatchNamespacedUuid' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchUuidUuid' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchNamespacedName'),
        new OA\Schema(ref: '#/components/schemas/HexbatchDomainedUuid' ),
        new OA\Schema(ref: '#/components/schemas/HexbatchDomainedName'),
    ]
)]
class HexbatchResource
{


}
