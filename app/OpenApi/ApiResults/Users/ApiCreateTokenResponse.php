<?php

namespace App\OpenApi\ApiResults\Users;


use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;

use App\OpenApi\Results\Users\CreateTokenResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the token and thing for the api call
 */
#[OA\Schema(schema: 'ApiCreateTokenResponse')]
class ApiCreateTokenResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Token')]
    public CreateTokenResponse $token;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
        string $auth_token,
         ?Thing $thing = null
    )
    {
        $this->token = new CreateTokenResponse(auth_token: $auth_token);

        if ($thing) {
            $this->thing = new ApiThingResponse(thing:$thing);
        }
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['token'] = $this->token;
        if ($this->thing) {
            $ret['thing'] = $this->thing;
        }
        return $ret;
    }

}
