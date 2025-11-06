<?php

namespace App\OpenApi\Results\Users;


use App\Data\ApiParams\OpenApi\Common\HexbatchToken;
use App\OpenApi\Results\ResultBase;
use OpenApi\Attributes as OA;

/**
 * Returns the token that is required other api calls
 */
#[OA\Schema(schema: 'LoginResponse')]
class LoginResponse extends ResultBase
{
    #[OA\Property(title: 'Message')]
    public string $message;

    #[OA\Property(title: 'Auth Token', type: HexbatchToken::class)]
    public string $auth_token;

    #[OA\Property(title: 'When the current token expires',format: 'date-time')]
    public ?string $token_expires_at = null;


    public function __construct(string $message, string $auth_token,?string $expiration_date = null)
    {
        parent::__construct();
        $this->message = $message;
        $this->auth_token = $auth_token;
        $this->token_expires_at = $expiration_date;
    }

    public  function toArray() : array  {
        $what = parent::toArray();
        $what['auth_token'] = $this->auth_token;
        $what['token_expires_at'] = $this->token_expires_at;
        $what['message'] = $this->message;
        return $what;
    }


}
