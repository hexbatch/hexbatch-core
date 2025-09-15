<?php

namespace App\OpenApi\ApiResults\Server;



use App\Models\Server;
use App\OpenApi\ApiCollectionBase;
use App\OpenApi\ApiResults\ThingResponse;
use App\OpenApi\Results\Servers\ServerResponse;
use Hexbatch\Things\Interfaces\IThingBaseResponse;
use Hexbatch\Things\Models\Thing;
use OpenApi\Attributes as OA;


/**
 * Shows the server details and thing for the api call
 */
#[OA\Schema(schema: 'ApiServerResponse')]
class ApiServerResponse extends ApiCollectionBase implements IThingBaseResponse
{

    #[OA\Property(title: 'Server')]
    public ServerResponse $server;

    #[OA\Property(title: 'Thing')]
    public ?ThingResponse $thing = null;


    public function __construct(
        Server $given_server,int $type_level = 0,int $attribute_level = 0,bool $b_show_namespace = false,?Thing $thing = null
    )
    {
        $this->server = new ServerResponse(given_server: $given_server,type_level: $type_level,attribute_level: $attribute_level,
            b_show_namespace: $b_show_namespace);

        if ($thing) {
            $this->thing = new ThingResponse(thing:$thing);
        }

    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['server'] = $this->server;
        if ($this->thing) {
            $ret['thing'] = $this->thing;
        }

        return $ret;
    }

}
