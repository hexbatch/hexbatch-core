<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Server;

use App\Enums\Server\TypeOfServerStatus;
use App\Enums\Sys\TypeOfAction;
use App\Models\ElementType;
use App\Models\Server;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;
use Illuminate\Support\Carbon;

/**
 * asking elsewhere for new credentials
 */
class ServerPromote extends Act\Cmd\Server
{
    const UUID = '3fc91919-845c-4a9a-8261-db6de25db4b4';
    const ACTION_NAME = TypeOfAction::CMD_SERVER_PROMOTE;

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Act\Cmd\Server::class,
        Act\SystemPrivilege::class
    ];

    const EVENT_CLASSES = [
        Evt\Elsewhere\ServerRegistered::class
    ];



    public static function createServer(
        ?ElementType              $given_type,
        ?UserNamespace              $given_namespace,
        string             $server_name ,
        string             $server_domain ,
        string             $server_url ,
        TypeOfServerStatus  $server_status = TypeOfServerStatus::UNKNOWN_SERVER,
        ?string             $access_token_expires_at = null,
        ?string             $server_access_token = null,
        ?string             $uuid = null,
        bool                $is_system = false
    )
    : Server
    {
        $server = new Server();

        if ($uuid) {
            $server->ref_uuid = $uuid;
        }

        $server->owning_namespace_id = $given_namespace?->id;
        $server->server_type_id = $given_type?->id;
        $server->server_status = $server_status ;

        if ($access_token_expires_at) {
            $server->access_token_expires_at = Carbon::parse($access_token_expires_at)->timezone('UTC')->toDateTimeString() ;
        }

        $server->server_access_token = $server_access_token ;
        $server->server_name = $server_name ;
        $server->server_domain = $server_domain ;
        $server->server_url = $server_url ;
        $server->is_system = $is_system ;



        $server->save();
        return $server;
    }



}

