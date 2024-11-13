<?php
namespace App\Api\Cmd\Design\Promote;

use App\Api\Cmd\Design\DesignParams;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Types\TypeOfLifecycle;
use App\Models\ElementType;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignPromote;

class SetupForSystem
{
    use DesignParams;

    public function setAccess(?TypeOfServerAccess $access): SetupForSystem
    {
        $this->access = $access;
        return $this;
    }
    public function setNamespaceId(?int $namespace_id): SetupForSystem
    {
        $this->namespace_id = $namespace_id;
        return $this;
    }

    public function setServerId(?int $server_id): SetupForSystem
    {
        $this->server_id = $server_id;
        return $this;
    }

    public function setUuid(?string $uuid): SetupForSystem
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setTypeName(?string $type_name): SetupForSystem
    {
        $this->type_name = $type_name;
        return $this;
    }

    public function setSystem(bool $system): SetupForSystem
    {
        $this->system = $system;
        return $this;
    }

    public function setFinalType(bool $final_type): SetupForSystem
    {
        $this->final_type = $final_type;
        return $this;
    }

    public function setLifecycle(TypeOfLifecycle $lifecycle): SetupForSystem
    {
        $this->lifecycle = $lifecycle;
        return $this;
    }

    public function doParamsAndResponse()  :ElementType {
        /**
         * @var DesignPromoteParams $promo_params
         */
        $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,DesignPromote::getClassUuid());
        $promo_params->fromCollection($this->makeCollection());

        /**
         * @type DesignPromoteResponse $promo_work
         */
        $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,DesignPromote::getClassUuid());

        $promo_results = $promo_work::doWork($promo_params);
        return $promo_results->getGeneratedType();
    }

}
