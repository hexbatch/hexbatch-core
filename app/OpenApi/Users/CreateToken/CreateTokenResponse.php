<?php

namespace App\OpenApi\Users\CreateToken;

use App\OpenApi\Users\HexbatchToken;
use OpenApi\Attributes as OA;

/**
 * Returns the token that is required other api calls
 */
#[OA\Schema(schema: 'CreateTokenResponse')]
class CreateTokenResponse
{
    #[OA\Property(ref: HexbatchToken::class, title: 'Auth Token', type: 'password')]
    public string $auth_token;

    public function __construct(string $auth_token)
    {
        $this->auth_token = $auth_token;
    }


}
