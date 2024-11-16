<?php
namespace App\Api\Cmd\Element\Promote;

use App\Api\Cmd\Element\BulkElementParams;
use App\Models\Element;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\ElementPromote;

class EleForSystem
{
    use BulkElementParams;



    public function setNsOwnerIds(array $ns_owner_ids): EleForSystem
    {
        $this->ns_owner_ids = $ns_owner_ids;
        return $this;
    }

    public function setDestinationSetIds(array $destination_set_ids): EleForSystem
    {
        $this->destination_set_ids = $destination_set_ids;
        return $this;
    }

    public function setNumberPerSet(?int $number_per_set): EleForSystem
    {
        $this->number_per_set = $number_per_set;
        return $this;
    }

    public function setUuids(array $uuids): EleForSystem
    {
        $this->uuids = $uuids;
        return $this;
    }

    public function setPhaseId(?int $phase_id): EleForSystem
    {
        $this->phase_id = $phase_id;
        return $this;
    }

    public function setParentTypeId(?int $parent_type_id): EleForSystem
    {
        $this->parent_type_id = $parent_type_id;
        return $this;
    }

    public function setSystem(?bool $system): EleForSystem
    {
        $this->system = $system;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function doParamsAndResponse() :Element {
        /**
         * @var ElementPromoteParams $promo_params
         */
        $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,ElementPromote::getClassUuid());
        $promo_params->fromCollection($this->makeCollection());

        /**
         * @type ElementPromoteResponse $promo_work
         */
        $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,ElementPromote::getClassUuid());

        /** @var ElementPromoteResponse $promo_results */
        $promo_results = $promo_work::doWork($promo_params);
        return $promo_results->getGeneratedElements()[0];
    }


}
