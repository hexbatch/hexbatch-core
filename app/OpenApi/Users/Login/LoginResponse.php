<?php

namespace App\OpenApi\Users\Login;


use App\OpenApi\Users\HexbatchToken;
use OpenApi\Attributes as OA;

/**
 * Returns the token that is required other api calls
 */
#[OA\Schema(schema: 'LoginResponse')]
class LoginResponse
{
    #[OA\Property(title: 'Message')]
    public string $message;

    #[OA\Property(ref: HexbatchToken::class, title: 'Auth Token', type: 'password')]
    public string $auth_token;

    /**
     * @param string $message
     * @param string $auth_token
     */
    public function __construct(string $message, string $auth_token)
    {
        $this->message = $message;
        $this->auth_token = $auth_token;
    }


}
