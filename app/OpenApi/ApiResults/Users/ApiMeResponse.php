<?php

namespace App\OpenApi\ApiResults\Users;


use App\Models\User;
use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ApiThingResponse;

use App\OpenApi\Results\Users\MeResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows who you are and thing for the api call
 */
#[OA\Schema(schema: 'ApiMeResponse')]
class ApiMeResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Me')]
    public MeResponse $me;

    #[OA\Property(title: 'Thing')]
    public ?ApiThingResponse $thing = null;


    public function __construct(
        ?User $user = null, bool $show_namespace = false,
         ?Thing $thing = null
    )
    {
        $this->me = new MeResponse(user: $user,show_namespace: $show_namespace);

        if ($thing) {
            $this->thing = new ApiThingResponse(thing:$thing);
        }
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['me'] = $this->me;
        if ($this->thing) {
            $ret['thing'] = $this->thing;
        }
        return $ret;
    }

}
