<?php
namespace App\Api\Cmd\Set\Promote;



use App\Api\Cmd\Set\SetParams;

use App\Models\ElementSet;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\St\SetPromote;


class SetForSystem
{
    use SetParams;


    public function setSystem(?bool $system): SetForSystem
    {
        $this->system = $system;
        return $this;
    }

    public function setContentElementIds(array $content_element_ids): SetForSystem
    {
        $this->content_element_ids = $content_element_ids;
        return $this;
    }
    public function setParentSetElementId(?int $parent_set_element_id): SetForSystem
    {
        $this->parent_set_element_id = $parent_set_element_id;
        return $this;
    }

    public function setUuid(?string $uuid): SetForSystem
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setHasEvents(?bool $has_events): SetForSystem
    {
        $this->has_events = $has_events;
        return $this;
    }


    public function doParamsAndResponse()  :ElementSet {
        /**
         * @var SetPromoteParams $promo_params
         */
        $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,SetPromote::getClassUuid());
        $promo_params->fromCollection($this->makeCollection());

        /**
         * @type SetPromoteResponse $promo_work
         */
        $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,SetPromote::getClassUuid());

        /**
         * @type SetPromoteResponse $promo_results
         */
        $promo_results = $promo_work::doWork($promo_params);
        return $promo_results->getGeneratedSet();
    }

}
