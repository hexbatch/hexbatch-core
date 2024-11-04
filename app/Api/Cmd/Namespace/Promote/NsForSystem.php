<?php
namespace App\Api\Cmd\Namespace\Promote;


use App\Exceptions\HexbatchInitException;
use App\Models\UserNamespace;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\NamespacePromote;
use Illuminate\Support\Collection;

class NsForSystem
{
    use NamespaceParams;


    public function makeCollection() : Collection {
        $arr = [];
        foreach ($this as $key => $val) {
            $arr[$key] = $val;
        }
        return new Collection($arr);
    }

    public function setNamespaceUserId(?int $namespace_user_id): NsForSystem
    {
        $this->namespace_user_id = $namespace_user_id;
        return $this;
    }

    public function setNamespaceServerId(?int $namespace_server_id): NsForSystem
    {
        $this->namespace_server_id = $namespace_server_id;
        return $this;
    }

    public function setNamespaceTypeId(?int $namespace_type_id): NsForSystem
    {
        $this->namespace_type_id = $namespace_type_id;
        return $this;
    }

    public function setPublicElementId(?int $public_element_id): NsForSystem
    {
        $this->public_element_id = $public_element_id;
        return $this;
    }

    public function setPrivateElementId(?int $private_element_id): NsForSystem
    {
        $this->private_element_id = $private_element_id;
        return $this;
    }

    public function setNamespaceHomeSetId(?int $namespace_home_set_id): NsForSystem
    {
        $this->namespace_home_set_id = $namespace_home_set_id;
        return $this;
    }

    public function setNamespacePublicKey(?string $namespace_public_key): NsForSystem
    {
        $this->namespace_public_key = $namespace_public_key;
        return $this;
    }

    public function setNamespaceName(?string $namespace_name): NsForSystem
    {
        $this->namespace_name = $namespace_name;
        return $this;
    }

    public function setUuid(?string $uuid): NsForSystem
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function doParamsAndResponse() :UserNamespace {
        try
        {

            /**
             * @var NamespacePromoteParams $promo_params
             */
            $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,NamespacePromote::getClassUuid());
            $promo_params->fromCollection($this->makeCollection());

            /**
             * @type NamespacePromoteResponse $promo_work
             */
            $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,NamespacePromote::getClassUuid());

            /**
             * @type NamespacePromoteResponse $promo_results
             */
            $promo_results = $promo_work::doWork($promo_params);
            return $promo_results->getGeneratedNamespace();

        } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
        }
    }



}
