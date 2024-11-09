<?php
namespace App\Api\Cmd\Phase\Promote;



use App\Api\Cmd\Phase\PhaseParams;


use App\Models\Phase;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ph\PhasePromote;


class PhaseForSystem
{
    use PhaseParams;


    public function setPhaseId(?int $phase_id): PhaseForSystem
    {
        $this->phase_id = $phase_id;
        return $this;
    }



    public function setPhaseTypeId(?int $phase_type_id): PhaseForSystem
    {
        $this->phase_type_id = $phase_type_id;
        return $this;
    }

    public function setEditedByPhaseId(?int $edited_by_phase_id): PhaseForSystem
    {
        $this->edited_by_phase_id = $edited_by_phase_id;
        return $this;
    }

    public function setDefaultPhase(?bool $default_phase): PhaseForSystem
    {
        $this->default_phase = $default_phase;
        return $this;
    }

    public function setUuid(?string $uuid): PhaseForSystem
    {
        $this->uuid = $uuid;
        return $this;
    }




    public function doParamsAndResponse()  :Phase {
        /**
         * @var PhasePromoteParams $promo_params
         */
        $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,PhasePromote::getClassUuid());
        $promo_params->fromCollection($this->makeCollection());

        /**
         * @type PhasePromoteResponse $promo_work
         */
        $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,PhasePromote::getClassUuid());

        /**
         * @type PhasePromoteResponse $promo_results
         */
        $promo_results = $promo_work::doWork($promo_params);
        return $promo_results->getGeneratedPhase();
    }

}
