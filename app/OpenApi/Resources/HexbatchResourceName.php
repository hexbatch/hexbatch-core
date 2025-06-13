<?php

namespace App\OpenApi\Resources;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_resource_name',
    title: 'Resource Name',
    description: 'Defines a resource name by itself',
    type: 'string',
    maxLength: 30,
    minLength: 3,
    pattern: '^\p{L}[\p{L}0-9_]{2,29}$',
    example: [new OA\Examples(summary: "No capital letters, no punctuation, underscores allowed, numbers allowed after first letter", value:'my_first_user221') ]
)]
class HexbatchResourceName
{


}
