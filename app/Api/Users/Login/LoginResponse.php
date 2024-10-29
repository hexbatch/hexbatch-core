<?php

namespace App\Api\Users\Login;

use App\Api\IApiOaResponse;
use OpenApi\Attributes as OA;

/**
 * todo use that library to make json out of this in the controller
 */
#[OA\Schema(schema: 'LoginResponse')]
class LoginResponse implements IApiOaResponse
{
    #[OA\Property(title: 'Message')]
    public string $message;

    #[OA\Property(title: 'Auth Token',type: 'password')]
    public string $auth_token;
}
