<?php

namespace App\OpenApi\Results\Users;

use App\OpenApi\Common\HexbatchToken;
use App\OpenApi\Results\ResultBase;
use OpenApi\Attributes as OA;

/**
 * Returns the token that is required other api calls
 */
#[OA\Schema(schema: 'CreateTokenResponse')]
class CreateTokenResponse extends ResultBase
{
    #[OA\Property(ref: HexbatchToken::class, title: 'Auth Token', type: 'password')]
    public string $auth_token;

    public function __construct(string $auth_token)
    {
        $this->auth_token = $auth_token;
    }

    public  function toArray() : array  {
        $what = parent::toArray();
        $what['auth_token'] = $this->auth_token;
        return $what;
    }

}
