<?php

namespace App\OpenApi\Results\Users;

use App\OpenApi\Results\ResultBase;
use OpenApi\Attributes as OA;

/**
 * Returns the token that is required other api calls
 */
#[OA\Schema(schema: 'CreateTokenResponse')]
class CreateTokenResponse extends ResultBase
{
    #[OA\Property(ref: '#/components/schemas/HexbatchToken', title: 'Auth Token', type: 'string')]
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
