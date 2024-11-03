<?php

namespace App\Api\Users\Login;

use App\Api\IApiOaResponse;
use App\Api\Users\HexbatchToken;
use OpenApi\Attributes as OA;

/**
 * Returns the token that is required other api calls
 */
#[OA\Schema(schema: 'LoginResponse')]
class LoginResponse implements IApiOaResponse
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
