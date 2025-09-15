<?php

namespace App\OpenApi\ApiResults\Users;


use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ThingResponse;

use App\OpenApi\Results\Users\LoginResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the login and thing for the api call
 */
#[OA\Schema(schema: 'ApiLoginResponse')]
class ApiLoginResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Token')]
    public LoginResponse $login;

    #[OA\Property(title: 'Thing')]
    public ?ThingResponse $thing = null;


    public function __construct(
        string $message, string $auth_token,
         ?Thing $thing = null
    )
    {
        $this->login = new LoginResponse(message: $message,auth_token: $auth_token);

        if ($thing) {
            $this->thing = new ThingResponse(thing:$thing);
        }
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['token'] = $this->login;
        if ($this->thing) {
            $ret['thing'] = $this->thing;
        }
        return $ret;
    }

}
