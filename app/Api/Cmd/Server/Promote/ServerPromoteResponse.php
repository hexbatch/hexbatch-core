<?php
namespace App\Api\Cmd\Server\Promote;


use App\Api\Cmd\Design\Promote\DesignPromoteResponse;
use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Exceptions\HexbatchInvalidException;
use App\Models\Server;
use App\Models\Thing;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Server\ServerPromote;

class ServerPromoteResponse extends ServerPromote implements IActionWorkReturn,IActionOaResponse,IActionWorker
{

    public function __construct(
        protected ?Server $generated_server = null
    )
    {
    }

    public function toThing(Thing $thing)
    {

    }

    protected function run(ServerPromoteParams $params) {
        if ($params->getServerId()) {
            $server = Server::findOrFail($params->getServerId());
        } else {
            $server = new Server();
        }

        if ($params->getUuid()) {
            $server->ref_uuid = $params->getUuid();
        }


        if ($params->getOwningNamespaceId()) {
            $server->owning_namespace_id = $params->getOwningNamespaceId();
        }


        if ($params->getServerTypeId()) {
            $server->server_type_id = $params->getServerTypeId();
        }


        if ($params->getServerStatus()) {
            $server->server_status = $params->getServerStatus() ;
        }


        if ($params->getAccessTokenExpiresAt()) {
            $server->access_token_expires_at = $params->getAccessTokenExpiresAt() ;
        }


        if ($params->getServerAccessToken()) {
            $server->server_access_token = $params->getServerAccessToken() ;
        }


        if ($params->getServerName()) {
            $server->server_name = $params->getServerName() ;
        }


        if ($params->getServerDomain()) {
            $server->server_domain = $params->getServerDomain() ;
        }

        if ($params->getServerUrl()) {
            $server->server_url = $params->getServerUrl() ;
        }


        if ($params->isSystem() !== null) {
            $server->is_system = $params->isSystem() ;
        }

        $server->save();
        $this->generated_server = $server;
    }

    /**
     * @param ServerPromoteParams $params
     * @return DesignPromoteResponse
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,ServerPromoteParams::class) || is_subclass_of($params,ServerPromoteParams::class))) {
            throw new HexbatchInvalidException("Params is not ServerPromoteParams");
        }
        $worker = new ServerPromoteResponse();
        $worker->run($params);
        return $worker;
    }

    public function getGeneratedServer(): ?Server
    {
        return $this->generated_server;
    }


}
