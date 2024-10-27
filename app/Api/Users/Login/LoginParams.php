<?php

namespace App\Api\Users\Login;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'LoginParams',example: [
    new OA\Examples(summary: "logging in example", value: ["username"=>"will","password"=>"xamp"]) ,
    new OA\Examples(summary: "logging 2 in example", value: ["username"=>"will2","password"=>"xamp22"]) ,
])]
class LoginParams
{
    #[OA\Property(title: 'User Name',
        example: [new OA\Examples(summary: "user name example", value:'will_fart') ]

    )]
    public string $username;

    #[OA\Property(title: 'Password',type: 'password')]
    public string $password;
}
