<?php

namespace App\Api\Calls\User;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_username',
    title: 'User Name',
    description: 'Defines a user name',
    type: 'string',
    maxLength: 30,
    minLength: 3,
    pattern: '^\p{L}[\p{L}0-9_]{2,}$',
    example: [new OA\Examples(summary: "No capital letters, no punctuation, underscores allowed, numbers allowed after first letter", value:'my_first_user221') ]
)]
class HexbatchUserName
{


}
