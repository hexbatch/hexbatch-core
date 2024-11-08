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
        // todo implement writing to thing method
    }

    protected function run(ServerPromoteParams $params) {
        $server = new Server();
        $server->ref_uuid = $params->getUuid();
        $server->owning_namespace_id = $params->getOwningNamespaceId();
        $server->server_type_id = $params->getServerTypeId();
        $server->server_status = $params->getServerStatus() ;
        $server->access_token_expires_at = $params->getAccessTokenExpiresAt() ;
        $server->server_access_token = $params->getServerAccessToken() ;
        $server->server_name = $params->getServerName() ;
        $server->server_domain = $params->getServerDomain() ;
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
