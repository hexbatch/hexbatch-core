<?php
namespace App\Api\Cmd\Element\PromoteEdit;

use App\Api\Cmd\Element\BulkElementParams;
use App\Api\Cmd\Element\Promote\ElementPromoteParams;
use App\Api\Cmd\Element\Promote\ElementPromoteResponse;
use App\Models\Element;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\ElementPromote;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\ElementPromoteEdit;

class EditEleForSystem
{
    use EditElementParams;


    public function setElementIds(array $element_ids): EditEleForSystem
    {
        $this->element_ids = $element_ids;
        return $this;
    }

    public function setPhaseId(?int $phase_id): EditEleForSystem
    {
        $this->phase_id = $phase_id;
        return $this;
    }

    public function setSetId(?int $set_id): EditEleForSystem
    {
        $this->set_id = $set_id;
        return $this;
    }

    public function setOwningNamespaceId(?int $owning_namespace_id): EditEleForSystem
    {
        $this->owning_namespace_id = $owning_namespace_id;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function doParamsAndResponse() :Element {
        /**
         * @var ElementEditPromoteParams $promo_params
         */
        $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,ElementPromoteEdit::getClassUuid());
        $promo_params->fromCollection($this->makeCollection());

        /**
         * @type ElementEditPromoteResponse $promo_work
         */
        $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,ElementPromoteEdit::getClassUuid());

        /** @var ElementEditPromoteResponse $promo_results */
        $promo_results = $promo_work::doWork($promo_params);
        return $promo_results->getGeneratedElements()[0];
    }


}
