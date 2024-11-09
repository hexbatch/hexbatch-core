<?php
namespace App\Api\Cmd\Server\Promote;


use App\Api\Cmd\Server\ServerParams;
use App\Enums\Server\TypeOfServerStatus;

use App\Models\Server;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Server\ServerPromote;

class ServerForSystem
{
    use ServerParams;


    public function setSystem(bool $system): ServerForSystem
    {
        $this->system = $system;
        return $this;
    }
    public function setOwningNamespaceId(?int $owning_namespace_id): ServerForSystem
    {
        $this->owning_namespace_id = $owning_namespace_id;
        return $this;
    }

    public function setServerTypeId(?int $server_type_id): ServerForSystem
    {
        $this->server_type_id = $server_type_id;
        return $this;
    }

    public function setUuid(?string $uuid): ServerForSystem
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setServerStatus(?TypeOfServerStatus $server_status): ServerForSystem
    {
        $this->server_status = $server_status;
        return $this;
    }

    public function setAccessTokenExpiresAt(?int $access_token_expires_at): ServerForSystem
    {
        $this->access_token_expires_at = $access_token_expires_at;
        return $this;
    }

    public function setServerName(?string $server_name): ServerForSystem
    {
        $this->server_name = $server_name;
        return $this;
    }

    public function setServerDomain(?string $server_domain): ServerForSystem
    {
        $this->server_domain = $server_domain;
        return $this;
    }

    public function setServerAccessToken(?string $server_access_token): ServerForSystem
    {
        $this->server_access_token = $server_access_token;
        return $this;
    }



    public function doParamsAndResponse()  :Server {
        /**
         * @var ServerPromoteParams $promo_params
         */
        $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,ServerPromote::getClassUuid());
        $promo_params->fromCollection($this->makeCollection());

        /**
         * @type ServerPromoteResponse $promo_work
         */
        $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,ServerPromote::getClassUuid());

        /**
         * @type ServerPromoteResponse $promo_results
         */
        $promo_results = $promo_work::doWork($promo_params);
        return $promo_results->getGeneratedServer();
    }

}
