<?php

namespace App\OpenApi\Common;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'HexbatchToken',
    title: 'A token used to authenticate',
    description: 'Defines a regular uuid',
    type: 'string',
    maxLength: 60,
    minLength: 40,
    example: [new OA\Examples(summary: "Tokens look like this, but not always", value:'2|jY8m3acW2GdDZlgJOxX2hWCjlRJ6lcu4OA3q3XWcc4cbc545') ]
)]
class HexbatchToken
{


}
