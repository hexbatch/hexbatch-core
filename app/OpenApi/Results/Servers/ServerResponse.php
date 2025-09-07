<?php

namespace App\OpenApi\Results\Servers;

use App\Enums\Server\TypeOfServerStatus;
use App\Helpers\Utilities;
use App\Models\Server;
use App\OpenApi\Common\HexbatchUuid;
use App\OpenApi\Results\ResultBase;
use App\OpenApi\Results\Types\TypeResponse;
use App\OpenApi\Results\UserNamespaces\UserNamespaceResponse;
use Carbon\Carbon;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\OpenApi\Things\ThingMimimalResponseTrait;
use OpenApi\Attributes as OA;


/**
 * Show details about a phase
 */
#[OA\Schema(schema: 'ServerResponse')]
class ServerResponse extends ResultBase
{
    use ThingMimimalResponseTrait;
    #[OA\Property(title: 'Server uuid',type: HexbatchUuid::class)]
    public string $uuid = '';

    #[OA\Property(title: 'Namespace uuid',type: HexbatchUuid::class)]
    public string $namespace_uuid = '';

    #[OA\Property(title: 'Type uuid',type: HexbatchUuid::class)]
    public string $type_uuid = '';

    #[OA\Property(title: 'Namespace')]
    public ?UserNamespaceResponse $namespace = null ;

    #[OA\Property(title: 'Type of server')]
    public ?TypeResponse $type = null ;

    #[OA\Property(title: 'Server status')]
    public TypeOfServerStatus $server_status  ;

    #[OA\Property(title: 'Server name')]
    public ?string $server_name  ;

    #[OA\Property(title: 'Server domain')]
    public ?string $server_domain  ;

    #[OA\Property(title: 'Server url')]
    public ?string $server_url  ;


    #[OA\Property(title: 'Server created at',format: 'date-time')]
    public ?string $created_at = '';

    #[OA\Property(title: 'Server version')]
    public ?string $server_version  ;




    public function __construct(Server $given_server,int $type_level = 0,int $attribute_level = 0,bool $b_show_namespace = false,?Thing $thing = null)
    {
        parent::__construct(thing: $thing);
        $this->uuid = $given_server->ref_uuid;
        $this->namespace_uuid = $given_server->owning_namespace->ref_uuid;
        $this->type_uuid = $given_server->server_type->ref_uuid;
        if ($b_show_namespace ) {
            /** @uses Server::owning_namespace()  */
            $this->namespace = new UserNamespaceResponse(namespace: $given_server->owning_namespace);
        }

        if ($type_level > 0 ) {
            /** @uses Server::server_type()  */
            $this->type = new TypeResponse(given_type: $given_server->server_type,
                parent_levels: $type_level ,attribute_levels: $attribute_level);
        }


        $this->server_status = $given_server->server_status;
        $this->server_name = $given_server->server_name;
        $this->server_url = $given_server->server_url;
        $this->server_domain = $given_server->server_domain;

        $this->created_at = $given_server->created_at? Carbon::parse($given_server->created_at,'UTC')
            ->timezone(config('app.timezone'))->toIso8601String():null;

        $this->server_version = Utilities::getVersionAsString();

    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['uuid'] = $this->uuid;
        $ret['server_name'] = $this->server_name;
        $ret['server_url'] = $this->server_url;
        $ret['server_domain'] = $this->server_domain;
        $ret['namespace_uuid'] = $this->namespace_uuid;
        $ret['server_status'] = $this->server_status;
        $ret['type_uuid'] = $this->type_uuid;
        $ret['created_at'] = $this->created_at;
        $ret['server_version'] = $this->server_version;



        if ($this->namespace) {
            $ret['namespace'] = $this->namespace;
        }

        if ($this->type) {
            $ret['type'] = $this->type;
        }
        return $ret;
    }

}
