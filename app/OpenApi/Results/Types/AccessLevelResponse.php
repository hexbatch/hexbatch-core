<?php

namespace App\OpenApi\Results\Types;

use App\Enums\Attributes\TypeOfServerAccess;
use App\Models\ElementTypeServerLevel;
use App\OpenApi\Common\HexbatchUuid;
use App\OpenApi\Results\ResultBase;
use App\OpenApi\Results\Servers\ServerResponse;
use Carbon\Carbon;
use OpenApi\Attributes as OA;


/**
 * Show details about a parent type
 */
#[OA\Schema(schema: 'AccessLevelResponse')]
class AccessLevelResponse extends ResultBase
{

    #[OA\Property(title: 'Type uuid',type: HexbatchUuid::class)]
    public string $type_uuid = '';

    #[OA\Property(title: 'Server uuid',type: HexbatchUuid::class)]
    public string $server_uuid = '';

    #[OA\Property(title: 'Server domain')]
    public string $server_domain ;



    #[OA\Property(title: 'Approval')]
    public TypeOfServerAccess $access;

    #[OA\Property(title: 'Access since',format: 'date-time')]
    public ?string $access_at = '';


    #[OA\Property(title: 'Server')]
    public ?ServerResponse $server = null;

    #[OA\Property(title: 'Type')]
    public ?TypeResponse $type = null;

    public function __construct( ElementTypeServerLevel $given_server_level , int $server_levels = 0,int $type_levels = 0 )
    {

        $this->access_at = $given_server_level->created_at?
                            Carbon::parse($given_server_level->created_at,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;

        $this->access = $given_server_level->access_type;

        if ($server_levels > 0) {
            /** @uses ElementTypeServerLevel::access_server() */
            $this->server = new ServerResponse(given_server: $given_server_level->access_server);
        }

        $this->server_domain = $given_server_level->access_server->server_domain;
        $this->server_uuid = $given_server_level->access_server->ref_uuid;
        $this->type_uuid = $given_server_level->type_having_access->ref_uuid;

        if ($type_levels > 0) {
            /** @uses ElementTypeServerLevel::type_having_access() */
            $this->type = new TypeResponse(given_type: $given_server_level->type_having_access);
        }


    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['type_uuid'] = $this->type_uuid;
        $ret['server_uuid'] = $this->server_uuid;
        $ret['access'] = $this->access->value;
        $ret['server_domain'] = $this->server_domain;

        if ($this->server) {
            $ret['server'] = $this->server;
        }

        if ($this->type) {
            $ret['type'] = $this->type;
        }
        return $ret;
    }

}
