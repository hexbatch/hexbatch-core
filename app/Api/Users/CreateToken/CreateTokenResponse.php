<?php

namespace App\Api\Users\CreateToken;

use App\Api\IApiOaResponse;
use App\Api\Users\HexbatchToken;
use OpenApi\Attributes as OA;

/**
 * Returns the token that is required other api calls
 */
#[OA\Schema(schema: 'CreateTokenResponse')]
class CreateTokenResponse implements IApiOaResponse
{
    #[OA\Property(ref: HexbatchToken::class, title: 'Auth Token', type: 'password')]
    public string $auth_token;

    public function __construct(string $auth_token)
    {
        $this->auth_token = $auth_token;
    }


}
