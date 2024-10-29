<?php

namespace App\Api\Users\Login;

use App\Api\IApiOaInput;
use OpenApi\Attributes as OA;

/**
 * todo find a way to convert the response body to fill in this automatically, so all routes can do that
 */
#[OA\Schema(schema: 'LoginParams',example: [
    new OA\Examples(summary: "logging in example", value: ["username"=>"will","password"=>"xamp"]) ,
    new OA\Examples(summary: "logging 2 in example", value: ["username"=>"will2","password"=>"xamp22"]) ,
])]
class LoginParams implements IApiOaInput
{
    #[OA\Property(title: 'User Name',
        example: [new OA\Examples(summary: "user name example", value:'will_fart') ]

    )]
    public string $username;

    #[OA\Property(title: 'Password',type: 'password')]
    public string $password;
}
