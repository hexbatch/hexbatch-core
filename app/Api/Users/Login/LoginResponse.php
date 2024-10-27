<?php

namespace App\Api\Users\Login;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'LoginResponse')]
class LoginResponse
{
    #[OA\Property(title: 'Message')]
    public string $message;

    #[OA\Property(title: 'Auth Token',type: 'password')]
    public string $auth_token;
}
