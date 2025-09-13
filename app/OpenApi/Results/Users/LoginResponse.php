<?php

namespace App\OpenApi\Results\Users;


use App\OpenApi\Results\ResultBase;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\OpenApi\Things\ThingMimimalResponseTrait;
use OpenApi\Attributes as OA;

/**
 * Returns the token that is required other api calls
 */
#[OA\Schema(schema: 'LoginResponse')]
class LoginResponse extends ResultBase
{
    use ThingMimimalResponseTrait;
    #[OA\Property(title: 'Message')]
    public string $message;

    #[OA\Property(ref: '#/components/schemas/HexbatchToken', title: 'Auth Token', type: 'password')]
    public string $auth_token;


    public function __construct(string $message, string $auth_token,?Thing $thing = null)
    {
        parent::__construct(thing: $thing);
        $this->message = $message;
        $this->auth_token = $auth_token;
    }

    public  function toArray() : array  {
        $what = parent::toArray();
        $what['auth_token'] = $this->auth_token;
        $what['message'] = $this->message;
        return $what;
    }


}
